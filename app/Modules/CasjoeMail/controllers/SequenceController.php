<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class SequenceController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    /**
     * List all sequences
     */
    public function index()
    {
        // Get all sequences for this tenant
        $stmt = $this->db->prepare("
            SELECT s.*,
                   COUNT(DISTINCT se.id) as enrolled_count,
                   COUNT(DISTINCT CASE WHEN se.status = 'completed' THEN se.id END) as completed_count,
                   COUNT(DISTINCT ss.id) as step_count
            FROM cm_sequences s
            LEFT JOIN cm_sequence_enrollments se ON s.id = se.sequence_id
            LEFT JOIN cm_sequence_steps ss ON s.id = ss.sequence_id
            WHERE s.tenant_id = ?
            GROUP BY s.id
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$this->tenantId]);
        $sequences = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/sequences/index.php';
    }

    /**
     * Show create form
     */
    public function create()
    {
        require __DIR__ . '/../Views/sequences/create.php';
    }

    /**
     * Store new sequence
     */
    public function store()
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $trigger = $_POST['trigger_event'] ?? 'manual';

        if (empty($name)) {
            header('Location: /mail/sequences/create?error=' . urlencode('Name is required'));
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO cm_sequences (tenant_id, name, description, trigger_event)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$this->tenantId, $name, $description, $trigger]);
            
            $sequenceId = $this->db->lastInsertId();
            
            header('Location: /mail/sequences/edit?id=' . $sequenceId . '&success=created');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences/create?error=' . urlencode('Failed to create sequence'));
            exit;
        }
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        // Get sequence
        $stmt = $this->db->prepare("
            SELECT * FROM cm_sequences 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([$id, $this->tenantId]);
        $sequence = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$sequence) {
            header('Location: /mail/sequences?error=' . urlencode('Sequence not found'));
            exit;
        }

        // Get steps
        $stmt = $this->db->prepare("
            SELECT * FROM cm_sequence_steps 
            WHERE sequence_id = ? 
            ORDER BY step_order ASC
        ");
        $stmt->execute([$id]);
        $steps = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/sequences/edit.php';
    }

    /**
     * Update sequence
     */
    public function update()
    {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $trigger = $_POST['trigger_event'] ?? 'manual';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $this->db->prepare("
                UPDATE cm_sequences 
                SET name = ?, description = ?, trigger_event = ?, is_active = ?
                WHERE id = ? AND tenant_id = ?
            ");
            $stmt->execute([$name, $description, $trigger, $isActive, $id, $this->tenantId]);

            header('Location: /mail/sequences/edit?id=' . $id . '&success=updated');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences/edit?id=' . $id . '&error=' . urlencode('Failed to update'));
            exit;
        }
    }

    /**
     * Delete sequence
     */
    public function delete()
    {
        $id = $_POST['id'] ?? 0;

        try {
            $stmt = $this->db->prepare("
                DELETE FROM cm_sequences 
                WHERE id = ? AND tenant_id = ?
            ");
            $stmt->execute([$id, $this->tenantId]);

            header('Location: /mail/sequences?success=' . urlencode('Sequence deleted'));
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to delete'));
            exit;
        }
    }

    /**
     * Duplicate sequence
     */
    public function duplicate()
    {
        $id = $_POST['id'] ?? 0;

        try {
            // Get original sequence
            $stmt = $this->db->prepare("
                SELECT * FROM cm_sequences 
                WHERE id = ? AND tenant_id = ?
            ");
            $stmt->execute([$id, $this->tenantId]);
            $original = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$original) {
                throw new \Exception('Sequence not found');
            }

            // Create duplicate
            $stmt = $this->db->prepare("
                INSERT INTO cm_sequences (tenant_id, name, description, trigger_event, is_active)
                VALUES (?, ?, ?, ?, 0)
            ");
            $newName = $original['name'] . ' (Copy)';
            $stmt->execute([$this->tenantId, $newName, $original['description'], $original['trigger_event']]);
            
            $newSequenceId = $this->db->lastInsertId();

            // Duplicate steps
            $stmt = $this->db->prepare("
                SELECT * FROM cm_sequence_steps 
                WHERE sequence_id = ? 
                ORDER BY step_order ASC
            ");
            $stmt->execute([$id]);
            $steps = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $insertStmt = $this->db->prepare("
                INSERT INTO cm_sequence_steps 
                (sequence_id, step_order, delay_days, delay_hours, subject, content, stop_on_reply, stop_on_click)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($steps as $step) {
                $insertStmt->execute([
                    $newSequenceId,
                    $step['step_order'],
                    $step['delay_days'],
                    $step['delay_hours'],
                    $step['subject'],
                    $step['content'],
                    $step['stop_on_reply'],
                    $step['stop_on_click']
                ]);
            }

            header('Location: /mail/sequences/edit?id=' . $newSequenceId . '&success=duplicated');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to duplicate: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Add step to sequence
     */
    public function addStep()
    {
        $sequenceId = $_POST['sequence_id'] ?? 0;
        $subject = $_POST['subject'] ?? '';
        $content = $_POST['content'] ?? '';
        $delayDays = (int)($_POST['delay_days'] ?? 0);
        $delayHours = (int)($_POST['delay_hours'] ?? 0);
        $stopOnReply = isset($_POST['stop_on_reply']) ? 1 : 0;
        $stopOnClick = isset($_POST['stop_on_click']) ? 1 : 0;

        // Get next step order
        $stmt = $this->db->prepare("
            SELECT COALESCE(MAX(step_order), 0) + 1 as next_order 
            FROM cm_sequence_steps 
            WHERE sequence_id = ?
        ");
        $stmt->execute([$sequenceId]);
        $nextOrder = $stmt->fetchColumn();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO cm_sequence_steps 
                (sequence_id, step_order, delay_days, delay_hours, subject, content, stop_on_reply, stop_on_click)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $sequenceId, $nextOrder, $delayDays, $delayHours, 
                $subject, $content, $stopOnReply, $stopOnClick
            ]);

            header('Location: /mail/sequences/edit?id=' . $sequenceId . '&success=step_added');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences/edit?id=' . $sequenceId . '&error=' . urlencode('Failed to add step'));
            exit;
        }
    }

    /**
     * Update step
     */
    public function updateStep()
    {
        $stepId = $_POST['step_id'] ?? 0;
        $subject = $_POST['subject'] ?? '';
        $content = $_POST['content'] ?? '';
        $delayDays = (int)($_POST['delay_days'] ?? 0);
        $delayHours = (int)($_POST['delay_hours'] ?? 0);
        $stopOnReply = isset($_POST['stop_on_reply']) ? 1 : 0;
        $stopOnClick = isset($_POST['stop_on_click']) ? 1 : 0;

        try {
            $stmt = $this->db->prepare("
                UPDATE cm_sequence_steps 
                SET subject = ?, content = ?, delay_days = ?, delay_hours = ?, 
                    stop_on_reply = ?, stop_on_click = ?
                WHERE id = ?
            ");
            $stmt->execute([$subject, $content, $delayDays, $delayHours, $stopOnReply, $stopOnClick, $stepId]);

            // Get sequence_id to redirect
            $stmt = $this->db->prepare("SELECT sequence_id FROM cm_sequence_steps WHERE id = ?");
            $stmt->execute([$stepId]);
            $sequenceId = $stmt->fetchColumn();

            header('Location: /mail/sequences/edit?id=' . $sequenceId . '&success=step_updated');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to update step'));
            exit;
        }
    }

    /**
     * Delete step
     */
    public function deleteStep()
    {
        $stepId = $_POST['step_id'] ?? 0;

        try {
            // Get sequence_id before deleting
            $stmt = $this->db->prepare("SELECT sequence_id FROM cm_sequence_steps WHERE id = ?");
            $stmt->execute([$stepId]);
            $sequenceId = $stmt->fetchColumn();

            // Delete step
            $stmt = $this->db->prepare("DELETE FROM cm_sequence_steps WHERE id = ?");
            $stmt->execute([$stepId]);

            // Reorder remaining steps
            $stmt = $this->db->prepare("
                SELECT id FROM cm_sequence_steps 
                WHERE sequence_id = ? 
                ORDER BY step_order ASC
            ");
            $stmt->execute([$sequenceId]);
            $remainingSteps = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $updateStmt = $this->db->prepare("UPDATE cm_sequence_steps SET step_order = ? WHERE id = ?");
            foreach ($remainingSteps as $index => $id) {
                $updateStmt->execute([$index + 1, $id]);
            }

            header('Location: /mail/sequences/edit?id=' . $sequenceId . '&success=step_deleted');
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to delete step'));
            exit;
        }
    }

    /**
     * Enroll subscribers in sequence
     */
    public function enroll()
    {
        $sequenceId = $_POST['sequence_id'] ?? 0;
        $subscriberIds = $_POST['subscriber_ids'] ?? [];

        if (!is_array($subscriberIds)) {
            $subscriberIds = [$subscriberIds];
        }

        $enrolled = 0;
        $skipped = 0;

        foreach ($subscriberIds as $subscriberId) {
            try {
                // Check if already enrolled
                $stmt = $this->db->prepare("
                    SELECT id FROM cm_sequence_enrollments 
                    WHERE subscriber_id = ? AND sequence_id = ?
                ");
                $stmt->execute([$subscriberId, $sequenceId]);
                
                if ($stmt->fetch()) {
                    $skipped++;
                    continue;
                }

                // Enroll
                $stmt = $this->db->prepare("
                    INSERT INTO cm_sequence_enrollments 
                    (subscriber_id, sequence_id, current_step, status, next_send_at)
                    VALUES (?, ?, 0, 'active', NOW())
                ");
                $stmt->execute([$subscriberId, $sequenceId]);
                $enrolled++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $message = "$enrolled subscriber(s) enrolled";
        if ($skipped > 0) {
            $message .= ", $skipped skipped (already enrolled)";
        }

        header('Location: /mail/sequences/enrollments?id=' . $sequenceId . '&success=' . urlencode($message));
        exit;
    }

    /**
     * Unenroll from sequence
     */
    public function unenroll()
    {
        $enrollmentId = $_POST['enrollment_id'] ?? 0;

        try {
            // Get sequence_id before deleting
            $stmt = $this->db->prepare("SELECT sequence_id FROM cm_sequence_enrollments WHERE id = ?");
            $stmt->execute([$enrollmentId]);
            $sequenceId = $stmt->fetchColumn();

            $stmt = $this->db->prepare("
                UPDATE cm_sequence_enrollments 
                SET status = 'stopped', stop_reason = 'manual'
                WHERE id = ?
            ");
            $stmt->execute([$enrollmentId]);

            header('Location: /mail/sequences/enrollments?id=' . $sequenceId . '&success=' . urlencode('Enrollment stopped'));
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to unenroll'));
            exit;
        }
    }

    /**
     * Pause enrollment
     */
    public function pauseEnrollment()
    {
        $enrollmentId = $_POST['enrollment_id'] ?? 0;

        try {
            $stmt = $this->db->prepare("
                UPDATE cm_sequence_enrollments 
                SET status = 'paused'
                WHERE id = ?
            ");
            $stmt->execute([$enrollmentId]);

            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/mail/sequences'));
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to pause'));
            exit;
        }
    }

    /**
     * Resume enrollment
     */
    public function resumeEnrollment()
    {
        $enrollmentId = $_POST['enrollment_id'] ?? 0;

        try {
            $stmt = $this->db->prepare("
                UPDATE cm_sequence_enrollments 
                SET status = 'active'
                WHERE id = ?
            ");
            $stmt->execute([$enrollmentId]);

            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/mail/sequences'));
            exit;
        } catch (\Exception $e) {
            header('Location: /mail/sequences?error=' . urlencode('Failed to resume'));
            exit;
        }
    }

    /**
     * Show enrollments for a sequence
     */
    public function enrollments()
    {
        $sequenceId = $_GET['id'] ?? 0;

        // Get sequence
        $stmt = $this->db->prepare("
            SELECT * FROM cm_sequences 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([$sequenceId, $this->tenantId]);
        $sequence = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$sequence) {
            header('Location: /mail/sequences?error=' . urlencode('Sequence not found'));
            exit;
        }

        // Get enrollments
        $stmt = $this->db->prepare("
            SELECT e.*, s.email, s.first_name, s.last_name,
                   ss.subject as current_step_subject
            FROM cm_sequence_enrollments e
            JOIN cm_subscribers s ON e.subscriber_id = s.id
            LEFT JOIN cm_sequence_steps ss ON e.sequence_id = ss.sequence_id 
                AND ss.step_order = e.current_step + 1
            WHERE e.sequence_id = ?
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute([$sequenceId]);
        $enrollments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/sequences/enrollments.php';
    }
}
