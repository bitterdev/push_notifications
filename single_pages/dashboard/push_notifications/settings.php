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
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\View\View;

/** @var int $welcomeMessageIconFileId */
/** @var string $welcomeMessageClickAction */
/** @var string $welcomeMessageBody */
/** @var string $welcomeMessageTitle */
/** @var string $welcomeMessageEnabled */
/** @var string $serverKey */
/** @var string $messagingSenderId */
/** @var string $storageBucket */
/** @var string $projectId */
/** @var string $databaseURL */
/** @var string $authDomain */
/** @var string $apiKey */
/** @var $app Application */
/** @var $form Form */
/** @var $fileSelector FileManager */
$fileSelector = $app->make(FileManager::class);
/** @var $pageSelector PageSelector */
$pageSelector = $app->make(PageSelector::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'push_notifications');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "push_notifications", "rateUrl" => "https://www.concrete5.org/marketplace/addons/push-notifications/reviews"], 'push_notifications');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "push_notifications"], 'push_notifications');
?>

<form action="#" method="post">
    <?php echo $token->output('save_settings'); ?>

    <fieldset>
        <legend>
            <?php echo t("General Settings"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("apiKey", t("Api Key")); ?>
            <?php echo $form->text("apiKey", $apiKey, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("authDomain", t("Auth Domain")); ?>
            <?php echo $form->text("authDomain", $authDomain, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("databaseURL", t("Database URL")); ?>
            <?php echo $form->text("databaseURL", $databaseURL, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("projectId", t("Project Id")); ?>
            <?php echo $form->text("projectId", $projectId, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("storageBucket", t("Storage Bucket")); ?>
            <?php echo $form->text("storageBucket", $storageBucket, ["class" => "form-control", "max-length" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("messagingSenderId", t("Messaging Sender Id")); ?>
            <?php echo $form->text("messagingSenderId", $messagingSenderId, ["class" => "form-control", "max-length" => 255]); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t("Extended Settings"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("serverKey", t("Server Key")); ?>
            <?php echo $form->text("serverKey", $serverKey, ["class" => "form-control", "max-length" => 255]); ?>

            <p class="text-muted">
                <?php echo t("You can find your server key by going to your project console on Firebase, clicking the gear icon on the right sidebar, selecting \"Project Settings\" and going to the \"Cloud Messaging\" tab."); ?>
            </p>
        </div>

    </fieldset>

    <fieldset>
        <legend>
            <?php echo t("Welcome Message"); ?>
        </legend>

        <div class="checkbox">
            <label>
                <?php echo $form->checkbox("welcomeMessageEnabled", 1, $welcomeMessageEnabled); ?>

                <?php echo t("Enabled Welcome Message"); ?>
            </label>
        </div>

        <div class="welcome-message <?php echo($welcomeMessageEnabled ? "" : " hidden"); ?>">
            <div class="form-group">
                <?php echo $form->label("welcomeMessageTitle", t("Title")); ?>
                <?php echo $form->text("welcomeMessageTitle", $welcomeMessageTitle, ["class" => "form-control", "max-length" => 255]); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("welcomeMessageBody", t("Body")); ?>
                <?php echo $form->textarea("welcomeMessageBody", $welcomeMessageBody, ["class" => "form-control", "max-length" => 255]); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("welcomeMessageClickAction", t("Click Action")); ?>
                <?php echo $pageSelector->selectPage("welcomeMessageClickAction", $welcomeMessageClickAction); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("welcomeMessageIconFileId", t("Icon File")); ?>
                <?php
                $welcomeMessageIconFile = null;

                if ($welcomeMessageIconFileId > 0) {
                    $welcomeMessageIconFile = File::getById($welcomeMessageIconFileId);
                }

                echo $fileSelector->image("welcomeMessageIconFileId", "welcomeMessageIconFileId", t("Please select icon file..."), $welcomeMessageIconFile);
                ?>
            </div>
        </div>
    </fieldset>

    <?php
    /** @noinspection PhpUnhandledExceptionInspection */
    View::element('/dashboard/did_you_know', ["packageHandle" => "push_notifications"], 'push_notifications');
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="pull-right btn btn-primary">
                <?php echo t('Save'); ?>
            </button>
        </div>
    </div>
</form>

<!--suppress JSUnresolvedVariable -->
<script>
    (function ($) {
        $(function () {
            $("#welcomeMessageEnabled").bind("change", function () {
                if ($(this).is(":checked")) {
                    $(".welcome-message").removeClass("hidden");
                } else {
                    $(".welcome-message").addClass("hidden");
                }
            });
        });
    })(jQuery);
</script>
