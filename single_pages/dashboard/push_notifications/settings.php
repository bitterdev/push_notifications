<?php /** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Form\Service\Form;

/** @var $form Form */
/** @var array $siteList */
/** @var array $enabledSites */

$app = Application::getFacadeApplication();
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
    <?php echo $token->output('update_settings'); ?>

    <fieldset>
        <legend>
            <?php echo t("Enabled Sites"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("enabledSites", t("Enabled Sites")); ?>
            <?php echo $form->selectMultiple("enabledSites", $siteList, $enabledSites); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="float-end btn btn-primary">
                <?php echo t('Save'); ?>
            </button>
        </div>
    </div>
</form>
