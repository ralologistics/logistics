<?php
require 'db.php';
function bindDynamicParams(mysqli_stmt $stmt, array $params)
{
    $types = '';
    $values = [];

    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_double($param)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $param;
    }

    $stmt->bind_param($types, ...$values);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;

    $company_code = $_POST['company_code'] ?? null;
    $name         = trim($_POST['name'] ?? '');
    $legal_name   = $_POST['legal_name'] ?? null;
    $email        = $_POST['email'] ?? null;
    $phone        = $_POST['phone'] ?? null;
    $mobile       = $_POST['mobile'] ?? null;
    $website      = $_POST['website'] ?? null;
    $country_id   = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null;
    $state        = $_POST['state'] ?? null;
    $city         = $_POST['city'] ?? null;
    $postcode     = $_POST['postcode'] ?? null;
    $address      = $_POST['address'] ?? null;
    $timezone     = $_POST['timezone'] ?? 'Europe/London';
    $currency     = $_POST['currency'] ?? 'GBP';
    $status       = isset($_POST['status']) ? 1 : 0;
    $notes        = $_POST['notes'] ?? null;

    // ========= LOGO =========
    $logo_path = null;
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $dir = 'uploads/logos/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $logo_path = $dir . time() . '_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);
    }

    if (empty($name)) {
        header("Location: company-form.php?error=Company name required");
        exit;
    }

    // ================= UPDATE =================
    if ($id) {

        $sql = "UPDATE companies SET
            company_code=?,
            name=?,
            legal_name=?,
            email=?,
            phone=?,
            mobile=?,
            website=?,
            country_id=?,
            state=?,
            city=?,
            postcode=?,
            address=?,
            logo=?,
            timezone=?,
            currency=?,
            status=?,
            notes=?
            WHERE id=?";

        $stmt = $conn->prepare($sql);

        bindDynamicParams($stmt, [
            $company_code,
            $name,
            $legal_name,
            $email,
            $phone,
            $mobile,
            $website,
            $country_id,
            $state,
            $city,
            $postcode,
            $address,
            $logo_path,
            $timezone,
            $currency,
            $status,
            $notes,
            (int)$id
        ]);

    }
    // ================= INSERT =================
    else {

        $sql = "INSERT INTO companies (
            company_code, name, legal_name, email, phone, mobile, website,
            country_id, state, city, postcode, address, logo,
            timezone, currency, status, notes
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        bindDynamicParams($stmt, [
            $company_code,
            $name,
            $legal_name,
            $email,
            $phone,
            $mobile,
            $website,
            $country_id,
            $state,
            $city,
            $postcode,
            $address,
            $logo_path,
            $timezone,
            $currency,
            $status,
            $notes
        ]);
    }

    // ================= EXECUTE =================
    if ($stmt->execute()) {
        header("Location: company-list.php");
        exit;
    } else {
        echo "<b>SQL Error:</b> " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
