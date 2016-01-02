<?php
require_once "../../config.php";
require_once $CFG->dirroot."/pdo.php";
require_once $CFG->dirroot."/lib/lms_lib.php";
require_once "data_util.php";
require_once "names.php";
require_once "locations.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LTI = LTIX::requireData();
$p = $CFG->dbprefix;

if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// All the assignments we support
$assignments = array(
    'regex_sum.php' => 'Sum with a Regular Expression',
    'http_headers.php' => 'Exploring HyperText Transport Protocol',
    'comment_html.php' => 'Sum comment data from HTML',
    'knows.php' => 'Follow links in a series of web pages.',
    'comment_xml.php' => 'Sum comment data from XML',
    'comment_json.php' => 'Sum comment data from JSON',
    'geo_json.php' => 'Retrieve GEO data from a JSON API'
);

$oldsettings = Settings::linkGetAll();

$assn = Settings::linkGet('exercise');

// Get any due date information
$dueDate = SettingsForm::getDueDate();

// Let the assignment handle the POST
if ( count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    require($assn);
    return;
}

// View
$OUTPUT->header();
$OUTPUT->bodyStart();

// Settings button and dialog

echo('<span style="position: fixed; right: 10px; top: 5px;">');
if ( $USER->instructor ) {
    echo('<a href="grades.php" target="_blank"><button class="btn btn-info">Grade detail</button></a> '."\n");
}
SettingsForm::button();
$OUTPUT->exitButton();
echo('</span>');

SettingsForm::start();
SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
SettingsForm::dueDate();
SettingsForm::done();
SettingsForm::end();

$OUTPUT->welcomeUserCourse();

$OUTPUT->flashMessages();

if ( $assn && isset($assignments[$assn]) ) {
    require($assn);
} else {
    if ( $USER->instructor ) {
        echo("<p>Please use settings to select an assignment for this tool.</p>\n");
    } else {
        echo("<p>This tool needs to be configured - please see your instructor.</p>\n");
    }
}
        

$OUTPUT->footer();

