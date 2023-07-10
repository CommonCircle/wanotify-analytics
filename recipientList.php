<?php
$recipients = array(
    "dlorigan@uw.edu",
    "lober@uw.edu",
    "jbaseman@uw.edu",
    "bryant.karras@doh.wa.gov",
    "andrea.king@doh.wa.gov",
    "amanda.higgins@doh.wa.gov",
    // "DOH-Web@doh.wa.gov",
    "lauram.west@doh.wa.gov",
);
$errorRecipients = array(
    "dlorigan@uw.edu",
);

$recipientList = implode(", ", $recipients);
$errorRecipientList = implode(", ", $errorRecipients);

// Testing override:
// $recipientList = "dlorigan@uw.edu";
