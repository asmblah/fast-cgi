<?php

print ($_GET['greeting'] ?? '(none)') . ' from the front controller, I had this POSTed: "' . $_POST['message'] . '"!';
