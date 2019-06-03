<?php
  if (empty($_GET['page']) || !is_numeric($_GET['page'])) $_GET['page'] = 1;

  if (isset($_POST['enable']) || isset($_POST['disable'])) {

    try {
      if (empty($_POST['users'])) throw new Exception(language::translate('error_must_select_users', 'You must select users'));

      foreach ($_POST['users'] as $user_id) {

        $user = new ent_user($user_id);
        $user->data['status'] = !empty($_POST['enable']) ? 1 : 0;
        $user->save();
      }

      notices::add('success', language::translate('success_changes_saved', 'Changes saved'));
      header('Location: '. document::link());
      exit;

    } catch (Exception $e) {
      notices::add('errors', $e->getMessage());
    }
  }

// Table Rows
  $users = array();

  $users_query = database::query(
    "select * from ". DB_TABLE_USERS ."
    order by username;"
  );

  if ($_GET['page'] > 1) database::seek($users_query, (settings::get('data_table_rows_per_page') * ($_GET['page']-1)));

  $page_items = 0;
  while ($user = database::fetch($users_query)) {
    $users[] = $user;
    if (++$page_items == settings::get('data_table_rows_per_page')) break;
  }

// Number of Rows
  $num_rows = database::num_rows($users_query);

// Pagination
  $num_pages = ceil($num_rows/settings::get('data_table_rows_per_page'));
?>

<div class="panel panel-app">
  <div class="panel-heading">
    <?php echo $app_icon; ?> <?php echo language::translate('title_users', 'Users'); ?>
  </div>

  <div class="panel-action">
    <ul class="list-inline">
      <li><?php echo functions::form_draw_link_button(document::link('', array('doc' => 'edit_user'), true), language::translate('title_create_new_user', 'Create New User'), '', 'add'); ?></li>
    </ul>
  </div>

  <div class="panel-body">
    <?php echo functions::form_draw_form_begin('users_form', 'post'); ?>

      <table class="table table-striped table-hover data-table">
        <thead>
          <tr>
            <th><?php echo functions::draw_fonticon('fa-check-square-o fa-fw checkbox-toggle', 'data-toggle="checkbox-toggle"'); ?></th>
            <th></th>
            <th class="main"><?php echo language::translate('title_username', 'Username'); ?></th>
            <th>&nbsp;</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($users as $user) { ?>
          <tr class="<?php echo empty($user['status']) ? 'semi-transparent' : null; ?>">
            <td><?php echo functions::form_draw_checkbox('users['. $user['id'] .']', $user['id']); ?></td>
            <td><?php echo functions::draw_fonticon('fa-circle', 'style="color: '. (!empty($user['status']) ? '#88cc44' : '#ff6644') .';"'); ?></td>
            <td><a href="<?php echo document::href_link('', array('doc' => 'edit_user', 'user_id' => $user['id']), true); ?>"><?php echo $user['username']; ?></a></td>
            <td class="text-right"><a href="<?php echo document::href_link('', array('doc' => 'edit_user', 'user_id' => $user['id']), true); ?>" title="<?php echo language::translate('title_edit', 'Edit'); ?>"><?php echo functions::draw_fonticon('fa-pencil'); ?></a></td>
          </tr>
          <?php }?>
        </tbody>

        <tfoot>
          <tr>
            <td colspan="4"><?php echo language::translate('title_users', 'Users'); ?>: <?php echo $num_rows; ?></td>
          </tr>
        </tfoot>
      </table>

      <div class="btn-group">
        <?php echo functions::form_draw_button('enable', language::translate('title_enable', 'Enable'), 'submit', '', 'on'); ?>
        <?php echo functions::form_draw_button('disable', language::translate('title_disable', 'Disable'), 'submit', '', 'off'); ?>
      </p>

    <?php echo functions::form_draw_form_end(); ?>
  </div>

  <div class="panel-footer">
    <?php echo functions::draw_pagination($num_pages); ?>
  </div>
</div>
