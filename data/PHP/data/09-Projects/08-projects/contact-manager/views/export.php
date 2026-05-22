<?php
// Export functionality
$contacts = $contact->getAll(1000); // Get all contacts for export
$format = $_GET['format'] ?? 'csv';
?>

<div class="card">
    <h1>Export Contacts</h1>
    
    <div style="margin-bottom: 20px;">
        <p>Export your contacts in various formats for backup or use in other applications.</p>
        <p><strong>Total Contacts:</strong> <?= count($contacts) ?></p>
    </div>
    
    <!-- Export Options -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card" style="padding: 15px;">
            <h4>CSV Export</h4>
            <p>Standard CSV format compatible with Excel, Google Sheets, and most spreadsheet applications.</p>
            <a href="?action=export&format=csv&download=1" class="btn btn-success">Download CSV</a>
        </div>
        
        <div class="card" style="padding: 15px;">
            <h4>JSON Export</h4>
            <p>JSON format for use in web applications and programming projects.</p>
            <a href="?action=export&format=json&download=1" class="btn btn-success">Download JSON</a>
        </div>
        
        <div class="card" style="padding: 15px;">
            <h4>vCard Export</h4>
            <p>vCard format for importing into contact management applications and mobile devices.</p>
            <a href="?action=export&format=vcard&download=1" class="btn btn-success">Download vCard</a>
        </div>
    </div>
    
    <!-- Preview -->
    <h3>Export Preview</h3>
    <p>Here's a preview of what your exported data will look like:</p>
    
    <?php if ($format === 'csv'): ?>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">
            <pre style="margin: 0; font-family: monospace; font-size: 0.9rem;">First Name,Last Name,Email,Phone,Company,Job Title,Address,Birthday,Notes,Category,Tags
<?php foreach (array_slice($contacts, 0, 3) as $contact): ?><?= htmlspecialchars($contact['first_name']) ?>,<?= htmlspecialchars($contact['last_name']) ?>,<?= htmlspecialchars($contact['email'] ?? '') ?>,<?= htmlspecialchars($contact['phone'] ?? '') ?>,<?= htmlspecialchars($contact['company'] ?? '') ?>,<?= htmlspecialchars($contact['job_title'] ?? '') ?>,<?= htmlspecialchars(str_replace(["\n", "\r"], " ", $contact['address'] ?? '')) ?>,<?= htmlspecialchars($contact['birthday'] ?? '') ?>,<?= htmlspecialchars(str_replace(["\n", "\r"], " ", $contact['notes'] ?? '')) ?>,<?= htmlspecialchars($contact['category_name'] ?? '') ?>,"<?= htmlspecialchars(is_array($contact['tags']) ? implode(', ', $contact['tags']) : '') ?>"
<?php endforeach; ?>
... (<?= count($contacts) ?> total contacts)</pre>
        </div>
    <?php elseif ($format === 'json'): ?>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">
            <pre style="margin: 0; font-family: monospace; font-size: 0.9rem;">{
  "contacts": [
<?php foreach (array_slice($contacts, 0, 2) as $index => $contact): ?>    {
      "id": <?= $contact['id'] ?>,
      "first_name": "<?= htmlspecialchars($contact['first_name']) ?>",
      "last_name": "<?= htmlspecialchars($contact['last_name']) ?>",
      "email": "<?= htmlspecialchars($contact['email'] ?? '') ?>",
      "phone": "<?= htmlspecialchars($contact['phone'] ?? '') ?>",
      "company": "<?= htmlspecialchars($contact['company'] ?? '') ?>",
      "job_title": "<?= htmlspecialchars($contact['job_title'] ?? '') ?>",
      "address": "<?= htmlspecialchars($contact['address'] ?? '') ?>",
      "birthday": "<?= htmlspecialchars($contact['birthday'] ?? '') ?>",
      "notes": "<?= htmlspecialchars($contact['notes'] ?? '') ?>",
      "category_name": "<?= htmlspecialchars($contact['category_name'] ?? '') ?>",
      "tags": <?= json_encode($contact['tags']) ?>
    }<?= $index < 1 ? ',' : '' ?>
<?php endforeach; ?>
  ],
  "total": <?= count($contacts) ?>,
  "export_date": "<?= date('Y-m-d H:i:s') ?>"
}</pre>
        </div>
    <?php elseif ($format === 'vcard'): ?>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">
            <pre style="margin: 0; font-family: monospace; font-size: 0.9rem;"><?php foreach (array_slice($contacts, 0, 2) as $contact): ?>BEGIN:VCARD
VERSION:3.0
FN:<?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?>
N:<?= htmlspecialchars($contact['last_name']) ?>;<?= htmlspecialchars($contact['first_name']) ?>;;;
<?php if ($contact['email']): ?>EMAIL:<?= htmlspecialchars($contact['email']) ?>
<?php endif; ?><?php if ($contact['phone']): ?>TEL:<?= htmlspecialchars($contact['phone']) ?>
<?php endif; ?><?php if ($contact['company']): ?>ORG:<?= htmlspecialchars($contact['company']) ?>
<?php endif; ?><?php if ($contact['job_title']): ?>TITLE:<?= htmlspecialchars($contact['job_title']) ?>
<?php endif; ?><?php if ($contact['address']): ?>ADR:;;<?= htmlspecialchars(str_replace("\n", " ", $contact['address'])) ?>;;;;
<?php endif; ?><?php if ($contact['birthday']): ?>BDAY:<?= date('Ymd', strtotime($contact['birthday'])) ?>
<?php endif; ?><?php if ($contact['notes']): ?>NOTE:<?= htmlspecialchars(str_replace("\n", "\\n", $contact['notes'])) ?>
<?php endif; ?>END:VCARD

<?php endforeach; ?>... (<?= count($contacts) ?> total contacts)</pre>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 20px;">
        <a href="?action=home" class="btn btn-secondary">Back to Contacts</a>
    </div>
</div>

<?php
// Handle actual download
if (isset($_GET['download'])) {
    $filename = 'contacts_export_' . date('Y-m-d_H-i-s');
    
    switch ($format) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            // CSV header
            echo "First Name,Last Name,Email,Phone,Company,Job Title,Address,Birthday,Notes,Category,Tags\n";
            
            // CSV data
            foreach ($contacts as $contact) {
                $tags = is_array($contact['tags']) ? implode(', ', $contact['tags']) : '';
                $address = str_replace(["\n", "\r"], " ", $contact['address'] ?? '');
                $notes = str_replace(["\n", "\r"], " ", $contact['notes'] ?? '');
                
                echo '"' . htmlspecialchars($contact['first_name']) . '","' . 
                     htmlspecialchars($contact['last_name']) . '","' . 
                     htmlspecialchars($contact['email'] ?? '') . '","' . 
                     htmlspecialchars($contact['phone'] ?? '') . '","' . 
                     htmlspecialchars($contact['company'] ?? '') . '","' . 
                     htmlspecialchars($contact['job_title'] ?? '') . '","' . 
                     htmlspecialchars($address) . '","' . 
                     htmlspecialchars($contact['birthday'] ?? '') . '","' . 
                     htmlspecialchars($notes) . '","' . 
                     htmlspecialchars($contact['category_name'] ?? '') . '","' . 
                     htmlspecialchars($tags) . "\"\n";
            }
            break;
            
        case 'json':
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '.json"');
            
            $exportData = [
                'contacts' => $contacts,
                'total' => count($contacts),
                'export_date' => date('Y-m-d H:i:s')
            ];
            
            echo json_encode($exportData, JSON_PRETTY_PRINT);
            break;
            
        case 'vcard':
            header('Content-Type: text/vcard');
            header('Content-Disposition: attachment; filename="' . $filename . '.vcf"');
            
            foreach ($contacts as $contact) {
                echo "BEGIN:VCARD\n";
                echo "VERSION:3.0\n";
                echo "FN:" . htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) . "\n";
                echo "N:" . htmlspecialchars($contact['last_name']) . ";" . htmlspecialchars($contact['first_name']) . ";;;\n";
                
                if ($contact['email']) {
                    echo "EMAIL:" . htmlspecialchars($contact['email']) . "\n";
                }
                
                if ($contact['phone']) {
                    echo "TEL:" . htmlspecialchars($contact['phone']) . "\n";
                }
                
                if ($contact['company']) {
                    echo "ORG:" . htmlspecialchars($contact['company']) . "\n";
                }
                
                if ($contact['job_title']) {
                    echo "TITLE:" . htmlspecialchars($contact['job_title']) . "\n";
                }
                
                if ($contact['address']) {
                    $address = str_replace("\n", " ", $contact['address']);
                    echo "ADR:;;" . htmlspecialchars($address) . ";;;;\n";
                }
                
                if ($contact['birthday']) {
                    echo "BDAY:" . date('Ymd', strtotime($contact['birthday'])) . "\n";
                }
                
                if ($contact['notes']) {
                    $notes = str_replace("\n", "\\n", $contact['notes']);
                    echo "NOTE:" . htmlspecialchars($notes) . "\n";
                }
                
                echo "END:VCARD\n\n";
            }
            break;
    }
    
    exit;
}
?>
