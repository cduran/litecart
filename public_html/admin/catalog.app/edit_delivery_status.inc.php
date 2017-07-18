<?php

  if (!empty($_GET['delivery_status_id'])) {
    $delivery_status = new ctrl_delivery_status($_GET['delivery_status_id']);
  } else {
    $delivery_status = new ctrl_delivery_status();
  }

  if (empty($_POST)) {
    foreach ($delivery_status->data as $key => $value) {
      $_POST[$key] = $value;
    }
  }

  breadcrumbs::add(!empty($delivery_status->data['id']) ? language::translate('title_edit_delivery_status', 'Edit Delivery Status') : language::translate('title_create_new_delivery_status', 'Create New Delivery Status'));

  if (isset($_POST['save'])) {

    if (empty($_POST['name'])) notices::add('errors', language::translate('error_must_enter_name', 'You must enter a name'));

    if (empty(notices::$data['errors'])) {

      $fields = array(
        'name',
        'description',
      );

      foreach ($fields as $field) {
        if (isset($_POST[$field])) $delivery_status->data[$field] = $_POST[$field];
      }

      $delivery_status->save();

      notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
      header('Location: '. document::link('', array('doc' => 'delivery_statuses'), true, array('delivery_status_id')));
      exit;
    }
  }

  if (isset($_POST['delete'])) {

    $delivery_status->delete();

    notices::add('success', language::translate('success_post_deleted', 'Post deleted'));
    header('Location: '. document::link('', array('doc' => 'delivery_statuses'), true, array('delivery_status_id')));
    exit;
  }

?>
<h1><?php echo $app_icon; ?> <?php echo !empty($delivery_status->data['id']) ? language::translate('title_edit_delivery_status', 'Edit Delivery Status') : language::translate('title_create_new_delivery_status', 'Create New Delivery Status'); ?></h1>

<?php echo functions::form_draw_form_begin('delivery_status_form', 'post', false, false, 'style="max-width: 640px;"'); ?>

  <div class="row">
    <div class="form-group col-md-6">
      <label><?php echo language::translate('title_name', 'Name'); ?></label>
      <?php foreach (array_keys(language::$languages) as $language_code)  echo functions::form_draw_regional_input_field($language_code, 'name['. $language_code .']', true, ''); ?>
    </div>
  </div>

  <div class="form-group">
    <label><?php echo language::translate('title_description', 'Description'); ?></label>
    <?php foreach (array_keys(language::$languages) as $language_code) echo functions::form_draw_regional_textarea($language_code, 'description['. $language_code .']', true, 'style="height: 50px;"'); ?>
  </div>

  <p class="btn-group">
    <?php echo functions::form_draw_button('save', language::translate('title_save', 'Save'), 'submit', '', 'save'); ?>
    <?php echo functions::form_draw_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"', 'cancel'); ?>
    <?php echo (isset($delivery_status->data['id'])) ? functions::form_draw_button('delete', language::translate('title_delete', 'Delete'), 'submit', 'onclick="if (!confirm(\''. language::translate('text_are_you_sure', 'Are you sure?') .'\')) return false;"', 'delete') : false; ?>
  </p>

<?php echo functions::form_draw_form_end(); ?>