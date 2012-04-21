#!/usr/bin/env php
<?php
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$api_version     = '0.1.0';
$api_state       = 'alpha';

$release_version = '0.1.1';
$release_state   = 'alpha';
$release_notes   = "This is an alpha release, see README.md for examples.";

$description = "This is an API wrapper for the useketchup.com API. You may use it" .
    " to create projects, meetings, items, notes and also user accounts to the" .
    " useketchup.com service.";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
        'filelistgenerator'       => 'file',
        'simpleoutput'            => true,
        'baseinstalldir'          => '/',
        'packagedirectory'        => './',
        'dir_roles'               => array(
            'Services'            => 'php',
            'Services/Useketchup' => 'php',
            'tests'               => 'test',
        ),
        'exceptions'              => array(
            'README.md'           => 'doc',
        ),
        'ignore'                  => array(
            'coverage/*',
            'package.php',
            '.git',
            '.gitignore',
            'tests/config.ini',
            '*.tgz'
        )
    )
);

$package->setPackage('Services_UseKetchup');
$package->setSummary('PHP API for useketchup.com');
$package->setDescription($description);
$package->setChannel('pear.php.net');
$package->setPackageType('php');
$package->setLicense(
    'New BSD License',
    'http://www.opensource.org/licenses/bsd-license.php'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$package->addMaintainer(
    'lead',
    'till',
    'Till Klampaeckel',
    'till@php.net'
);

$files = array(
// classes
    'Services/UseKetchup.php', 'Services/UseKetchup/Common.php',
    'Services/UseKetchup/Items.php', 'Services/UseKetchup/Meetings.php',
    'Services/UseKetchup/Notes.php', 'Services/UseKetchup/Projects.php',
    'Services/UseKetchup/User.php',
// tests
    'tests/AllTests.php', 'tests/config.ini-dist', 'tests/UseKetchupBaseTestCase.php',
    'tests/UseKetchupMeetingsTestCase.php', 'tests/UseKetchupProjectsTestCase.php',
    'tests/UseKetchupUserTestCase.php', 'tests/UseKetchupItemsTestCase.php',
    'tests/UseKetchupNotesTestCase.php', 'tests/UseKetchupTestCase.php',
);

foreach ($files as $file) {

    $package->addReplacement(
        $file,
        'package-info',
        '@name@',
        'name'
    );

    $package->addReplacement(
        $file,
        'package-info',
        '@package_version@',
        'version'
    );
}

$package->setPhpDep('5.2.1');

$package->addPackageDepWithChannel(
    'required',
    'PEAR_Exception',
    'pear.php.net'
);

$package->addPackageDepWithChannel(
    'required',
    'HTTP_Request2',
    'pear.php.net',
    '0.5.1'
);

$package->setPearInstallerDep('1.7.0');
$package->generateContents();
$package->addRelease();

if (   isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}

?>
