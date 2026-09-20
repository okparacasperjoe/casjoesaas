<?php
$content = file_get_contents('app/Modules/CasjoeERP/Views/crm/pipeline.php');

$bad_part = <<<HTML
                                        <?= htmlspecialchars((\$lead['company'] ?? \$lead['company_name'] ?? '') ?: 'Individual') ?>
                        <?php endforeach; ?>
                    </div>
HTML;

$good_part = <<<HTML
                                        <?= htmlspecialchars((\$lead['company'] ?? \$lead['company_name'] ?? '') ?: 'Individual') ?>
                                    </div>
                                </div>
                                <button style="background:transparent; border:none; color:rgba(255,255,255,0.4); cursor:pointer; padding:0;" title="Edit Lead">
                                    <ion-icon name="ellipsis-horizontal"></ion-icon>
                                </button>
                            </div>
                            
                            <?php
                                \$score = (int)(\$lead['ai_score'] ?? 0);
                                if (\$score <= 20) { \$c = '#94a3b8'; \$lbl = 'Cold'; }
                                elseif (\$score <= 40) { \$c = '#f59e0b'; \$lbl = 'Warm'; }
                                elseif (\$score <= 60) { \$c = '#f97316'; \$lbl = 'Hot'; }
                                elseif (\$score <= 80) { \$c = '#ef4444'; \$lbl = 'Very Hot'; }
                                else { \$c = '#8b5cf6'; \$lbl = 'On Fire'; }
                            ?>
                            <div style="display:flex; align-items:center; gap:6px; margin:8px 0;">
                                <span style="background:<?= \$c ?>; color:#fff; padding:2px 6px; border-radius:50px; font-size:0.7rem; font-weight:700; min-width:24px; text-align:center;"><?= \$score ?></span>
                                <small style="color:<?= \$c ?>; font-weight:600; font-size:0.75rem;"><?= \$lbl ?></small>
                            </div>

                            <div class="lead-footer">
                                <div class="lead-value">NGN <?= number_format(\$lead['value'] ?? 0, 2) ?></div>
                                <div class="lead-avatar" title="<?= htmlspecialchars(\$lead['name'] ?? '') ?>">
                                    <?= strtoupper(substr(\$lead['name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
HTML;

$content = str_replace($bad_part, $good_part, $content);
file_put_contents('app/Modules/CasjoeERP/Views/crm/pipeline.php', $content);
echo "Fixed pipeline.php\n";
