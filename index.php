<?php
if (!isset($_FILES['userFile'])) {
    echo '
    <form enctype="multipart/form-data" method="post">
        <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
        Envoyez ce fichier : <input name="userFile" type="file" accept="application/pdf" />
        <input type="submit" />
    </form>';
}
else {
    require 'vendor/autoload.php';

    //extend class for footer
    class PDF extends \fpdi\FPDI
    {
        function Footer()
        {
            // Don't add to the first page
            if ($this->PageNo() > 1) {
                // Go to 2.2 cm from bottom
                $this->SetY(-22);
                // Select Arial italic 12
                $this->SetFont('Arial', 'I', 12);
                // Print centered page number
                $this->Cell(0, 14, decbin($this->PageNo()), 0, 0, 'C');
            }
        }
    }

    // initiate FPDI
    $pdf = new PDF();

    // get the page count
    $pageCount = $pdf->setSourceFile($_FILES['userFile']['tmp_name']);
    // iterate through all pages
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        // import a page
        $templateId = $pdf->importPage($pageNo);
        // get the size of the imported page
        $size = $pdf->getTemplateSize($templateId);

        // create a page (landscape or portrait depending on the imported page size)
        if ($size['w'] > $size['h']) {
            $pdf->AddPage('L', array($size['w'], $size['h']));
        } else {
            $pdf->AddPage('P', array($size['w'], $size['h']));
        }

        // use the imported page
        $pdf->useTemplate($templateId);
    }

    // Output the new PDF
    $pdf->Output();
}