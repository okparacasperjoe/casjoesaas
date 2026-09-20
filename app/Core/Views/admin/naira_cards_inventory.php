<?php 
$title = "Naira Cards Inventory | Super Admin"; 
include __DIR__ . '/header.php';
?>

<style>
/* Dashboard grid layout required since it's not globally defined in style.css */
.admin-dashboard { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); 
    gap: 20px; 
    padding: 24px 0px; 
    width: 100%;
    box-sizing: border-box;
}
.admin-card { 
    background: rgba(18, 20, 38, 0.7) !important; 
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    padding: 18px 14px !important; 
    border-radius: 14px !important; 
    border: 1px solid rgba(255, 255, 255, 0.08) !important; 
    text-align: center; 
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
    text-decoration: none; 
    color: #eeeeee !important; 
    display: block; 
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3) !important;
    position: relative;
}
.admin-card:hover { 
    transform: translateY(-4px); 
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5), 0 0 16px rgba(255, 166, 0, 0.15) !important; 
    border-color: rgba(255, 166, 0, 0.4) !important; 
    background: rgba(24, 27, 50, 0.85) !important;
}
.admin-icon { 
    font-size: 2.2rem; 
    margin-bottom: 10px; 
    display: inline-block;
    transition: transform 0.3s ease;
}
.admin-card:hover .admin-icon {
    transform: scale(1.1);
}
.admin-card h3 { 
    margin: 0 0 6px 0 !important; 
    color: #ffffff !important; 
    font-size: 0.95rem !important;
    font-weight: 700 !important;
}
.admin-card p { 
    color: #94a3b8 !important; 
    font-size: 0.78rem !important; 
    margin: 0 !important;
}
.stat-value { 
    font-size: 2rem; 
    font-weight: 800; 
    color: #FFA600; 
    display: block; 
    margin: 5px 0; 
    font-family: 'Outfit', sans-serif;
    text-shadow: 0 2px 10px rgba(255, 166, 0, 0.2);
}
</style>


<div style="padding: 30px;">
<div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><ion-icon name="card-outline" style="color: #FFA600; vertical-align: middle;"></ion-icon> Naira Cards Inventory</h1>
        <p style="color: #cbd5e1; margin-top: 5px; font-size: 0.9rem;">Manage physical ATM card PANs for CasjoePay users.</p>
    </div>
    <button class="btn btn-warning" onclick="document.getElementById('addCardModal').style.display='flex';" style="background: #FFA600; color: #000; font-weight: bold; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer;">
        <ion-icon name="add-circle-outline" style="vertical-align: middle; font-size: 1.2rem; margin-right: 5px;"></ion-icon> Add New Card
    </button>
</div>

<!-- Casjoe Admin Stats Grid -->
<div class="admin-dashboard" style="padding: 10px 0 30px 0;">
    <div class="admin-card">
        <ion-icon name="layers-outline" class="admin-icon" style="color: #0984e3;"></ion-icon>
        <h3>Total Cards</h3>
        <span class="stat-value"><?= number_format($stats['total'] ?? 0) ?></span>
        <p>In Inventory</p>
    </div>
    
    <div class="admin-card">
        <ion-icon name="checkmark-circle-outline" class="admin-icon" style="color: #10ac84;"></ion-icon>
        <h3>Available</h3>
        <span class="stat-value"><?= number_format($stats['available'] ?? 0) ?></span>
        <p>Ready to Issue</p>
    </div>
    
    <div class="admin-card">
        <ion-icon name="person-outline" class="admin-icon" style="color: #eccc68;"></ion-icon>
        <h3>Assigned</h3>
        <span class="stat-value"><?= number_format($stats['assigned'] ?? 0) ?></span>
        <p>Issued to Users</p>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background: rgba(16, 172, 132, 0.15); border: 1px solid rgba(16, 172, 132, 0.3); color: #10ac84; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center;">
        <ion-icon name="checkmark-circle" style="font-size: 1.5rem; margin-right: 10px;"></ion-icon>
        <?php 
        if ($_GET['success'] === 'added') echo "Physical card successfully added to inventory.";
        if ($_GET['success'] === 'deleted') echo "Card successfully removed from inventory.";
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div style="background: rgba(214, 48, 49, 0.15); border: 1px solid rgba(214, 48, 49, 0.3); color: #ff7675; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center;">
        <ion-icon name="alert-circle" style="font-size: 1.5rem; margin-right: 10px;"></ion-icon>
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<!-- Inventory Table -->
<div class="card">
    <div style="padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
        <h3 style="margin: 0; font-size: 1.2rem; color: #fff;">Inventory List</h3>
    </div>
    <div class="table-container">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>PAN (Card Number)</th>
                    <th>Brand</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Date Added</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventory)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px 20px; color: #888;">
                            <ion-icon name="card-outline" style="font-size: 3rem; opacity: 0.3;"></ion-icon>
                            <p style="margin-top: 10px;">No physical cards found in inventory.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inventory as $card): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($card['pan']) ?></strong>
                            </td>
                            <td>
                                <?php if (strtolower($card['brand']) === 'afrigo'): ?>
                                    <span class="badge" style="background: rgba(16, 172, 132, 0.15); color: #10ac84; border-color: rgba(16, 172, 132, 0.3); padding: 4px 10px; border-radius: 20px;">
                                        <ion-icon name="card" style="vertical-align: middle; margin-right: 3px;"></ion-icon> AfriGo
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(9, 132, 227, 0.15); color: #0984e3; border-color: rgba(9, 132, 227, 0.3); padding: 4px 10px; border-radius: 20px;">
                                        <ion-icon name="card" style="vertical-align: middle; margin-right: 3px;"></ion-icon> <?= htmlspecialchars($card['brand']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($card['status'] === 'available'): ?>
                                    <span style="color: #10ac84; font-weight: bold; font-size: 0.9rem;"><span style="display:inline-block; width:8px; height:8px; background:#10ac84; border-radius:50%; margin-right:5px;"></span>Available</span>
                                <?php elseif ($card['status'] === 'assigned'): ?>
                                    <span style="color: #eccc68; font-weight: bold; font-size: 0.9rem;"><span style="display:inline-block; width:8px; height:8px; background:#eccc68; border-radius:50%; margin-right:5px;"></span>Assigned</span>
                                <?php else: ?>
                                    <span style="color: #d63031; font-weight: bold; font-size: 0.9rem;"><span style="display:inline-block; width:8px; height:8px; background:#d63031; border-radius:50%; margin-right:5px;"></span>Damaged</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($card['status'] === 'assigned'): ?>
                                    <strong><?= htmlspecialchars($card['assigned_user_name'] ?? '') ?></strong><br>
                                    <small style="color: #888;"><?= htmlspecialchars($card['assigned_user_email'] ?? '') ?></small>
                                <?php else: ?>
                                    <span style="color: #888;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #888; font-size: 0.9rem;">
                                <?= date('M d, Y', strtotime($card['created_at'])) ?><br>
                                <small><?= date('h:i A', strtotime($card['created_at'])) ?></small>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($card['status'] === 'available'): ?>
                                    <form action="/<?= ADMIN_PATH ?>/naira-cards-inventory/delete" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this PAN from inventory?');">
                                        <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                        <button type="submit" style="background: rgba(214, 48, 49, 0.1); border: 1px solid rgba(214, 48, 49, 0.3); color: #ff7675; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                                            <ion-icon name="trash-outline" style="vertical-align: middle;"></ion-icon> Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #888; padding: 6px 12px; border-radius: 6px; cursor: not-allowed; font-size: 0.9rem;" disabled title="Only available cards can be deleted">
                                        <ion-icon name="lock-closed-outline" style="vertical-align: middle;"></ion-icon> Locked
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Modal Setup -->
<div id="addCardModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: #13141f; width: 100%; max-width: 500px; border-radius: 16px; border: 1px solid rgba(255,166,0,0.3); box-shadow: 0 15px 40px rgba(0,0,0,0.5); overflow: hidden;">
        <div style="padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.3rem; color: #fff;"><ion-icon name="add-circle" style="color: #FFA600; vertical-align: middle; margin-right: 8px;"></ion-icon> Add Physical Card</h3>
            <button onclick="document.getElementById('addCardModal').style.display='none';" style="background: transparent; border: none; color: #888; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="/<?= ADMIN_PATH ?>/naira-cards-inventory/add" method="POST">
            <div style="padding: 25px;">
                <div style="background: rgba(9, 132, 227, 0.1); border-left: 3px solid #0984e3; color: #cbd5e1; padding: 15px; border-radius: 4px; margin-bottom: 25px; font-size: 0.9rem;">
                    <ion-icon name="information-circle" style="color: #0984e3; vertical-align: middle; font-size: 1.2rem;"></ion-icon> Enter the PAN exactly as it appears on the physical card.
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Card PAN (Number)</label>
                    <input type="text" name="pan" placeholder="e.g. 5061 1234 5678 9012" required style="width: 100%; background: #0c0d14; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 12px 15px; border-radius: 8px; font-size: 1rem; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="display: block; color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 8px;">Card Brand</label>
                    <select name="brand" required style="width: 100%; background: #0c0d14; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 12px 15px; border-radius: 8px; font-size: 1rem; box-sizing: border-box; cursor: pointer;">
                        <option value="AfriGo">AfriGo</option>
                        <option value="Verve">Verve</option>
                    </select>
                </div>
            </div>
            <div style="padding: 20px 25px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: flex-end; gap: 15px; background: rgba(0,0,0,0.2);">
                <button type="button" onclick="document.getElementById('addCardModal').style.display='none';" style="background: transparent; border: 1px solid rgba(255,255,255,0.1); color: #cbd5e1; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">Cancel</button>
                <button type="submit" style="background: #FFA600; border: none; color: #000; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    <ion-icon name="save-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Save to Inventory
                </button>
            </div>
        </form>
    </div>
</div>



</div>
<?php include __DIR__ . '/footer.php'; ?>
