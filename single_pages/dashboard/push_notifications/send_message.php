<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Form\Service\Form;

/** @var $form Form */

$app = Application::getFacadeApplication();
/** @var $pageSelector PageSelector */
/** @noinspection PhpUnhandledExceptionInspection */
$pageSelector = $app->make("helper/form/page_selector");
/** @var $fileSelector FileManager */
/** @noinspection PhpUnhandledExceptionInspection */
$fileSelector = $app->make(FileManager::class);
/** @var Token $token */
/** @noinspection PhpUnhandledExceptionInspection */
$token = $app->make(Token::class);
?>


<div class="ccm-dashboard-header-buttons">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element("dashboard/help", [], "push_notifications"); ?>
</div>

<?php /** @noinspection PhpUnhandledExceptionInspection */
View::element("dashboard/did_you_know", [], "push_notifications"); ?>

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
            <?php echo $form->label("targetPage", t("Click Action")); ?>
            <?php echo $pageSelector->selectPage("targetPage", null); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("iconFile", t("Icon File")); ?>
            <?php echo $fileSelector->image("iconFile", "iconFile", t("Please select icon file...")); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="float-end btn btn-primary">
                <?php echo t('Send Message'); ?>
            </button>
        </div>
    </div>
</form>
