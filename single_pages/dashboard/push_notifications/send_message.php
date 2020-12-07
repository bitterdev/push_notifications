<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Application;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\View\View;
use Concrete\Core\Form\Service\Form;

/** @var $app Application */
/** @var $form Form */

/** @var $pageSelector PageSelector */
$pageSelector = $app->make("helper/form/page_selector");
/** @var $fileSelector FileManager */
$fileSelector = $app->make(FileManager::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'push_notifications');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "push_notifications", "rateUrl" => "https://www.concrete5.org/marketplace/addons/push-notifications/reviews"], 'push_notifications');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "push_notifications"], 'push_notifications');
?>

<form action="#" method="post">
    <?php echo $token->output('send_message'); ?>

    <fieldset>
        <legend>
            <?php echo t("Send Message"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("title", t("Title")); ?>
            <?php echo $form->text("title", null, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("body", t("Body")); ?>
            <?php echo $form->textarea("body", null, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("destPageId", t("Click Action")); ?>
            <?php echo $pageSelector->selectPage("destPageId", null); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("iconFileId", t("Icon File")); ?>
            <?php echo $fileSelector->image("iconFileId", "iconFileId", t("Please select icon file..."), null); ?>
        </div>
    </fieldset>

    <?php
    /** @noinspection PhpUnhandledExceptionInspection */
    View::element('/dashboard/did_you_know', ["packageHandle" => "push_notifications"], 'push_notifications');
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="pull-right btn btn-primary">
                <?php echo t('Send Message'); ?>
            </button>
        </div>
    </div>
</form>
