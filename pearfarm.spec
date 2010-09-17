<?php

$notes = "* bugfix: Services_UseKetchup_Common::accept() (did not exit after HTTP_Request2 was injected)"
    . "\n" . "* bugfix: make sure the custom HTTP_Request2 is injected into subs"
    . "\n" . "* refactoring: moved getApiToken() call from __construct()"
    . "\n" . "* improved documentation"
    . "\n" . "* added ublic getClient() method";

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('Services_UseKetchup')
             ->setChannel('till.pearfarm.org')
             ->setSummary('PHP API for useketchup.com')
             ->setDescription('This is an API wrapper for the useketchup.com API. You may use it to create projects, meetings, items, notes and also user accounts to the useketchup.com service.')
             ->setReleaseVersion('0.1.0')
             ->setReleaseStability('alpha')
             ->setApiVersion('0.1.0')
             ->setApiStability('alpha')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_BSD)
             ->setNotes($notes)
             ->addMaintainer('lead', 'Till Klampaeckel', 'till', 'till@php.net')
             ->addGitFiles()
             ->addExecutable('Services_UseKetchup');