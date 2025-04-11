<?php
$page_title = "Market RSVP";
require_once 'private/initialize.php';
require_once 'private/header.php';
require_once 'private/functions.php';
require_login();

if (!Session::is_vendor()) {
  redirect_to("login.php");
}

$vendor = Vendor::find_by_user_id($session->user_id());
if (!$vendor || $vendor->vendor_status !== 'approved') {
  redirect_to("index.php");
}

$weeks = Admin::fetchUpcomingMarkets();
$rsvp_map = Admin::fetchVendorRsvps($vendor->vendor_id);

?>
<?php require_once 'private/popup_message.php'; ?>

<h2>RSVP for Upcoming Markets</h2>

<?php if (empty($weeks)): ?>
  <p>No upcoming markets available.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Week Start</th>
      <th>Week End</th>
      <th>RSVP Status</th>
      <th>Deadline</th>
      <th>Action</th>
    </tr>

    <?php foreach ($weeks as $week): ?>
      <tr>
        <td><?= h($week['week_start']) ?></td>
        <td><?= h($week['week_end']) ?></td>
        <td><?= isset($rsvp_map[$week['week_id']]) ? ucfirst($rsvp_map[$week['week_id']]) : 'Not RSVPed' ?></td>
        <td><?= h($week['confirmation_deadline']) ?></td>
        <td>
          <?php if ($week['confirmation_deadline'] >= date('Y-m-d')): ?>
            <form method="post" action="rsvp_action.php">
              <input type="hidden" name="week_id" value="<?= h($week['week_id']) ?>">
              <select name="status" onchange="this.form.submit()">
                <option value="planned" <?= ($rsvp_map[$week['week_id']] ?? '') === 'planned' ? 'selected' : '' ?>>Planned</option>
                <option value="confirmed" <?= ($rsvp_map[$week['week_id']] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="canceled" <?= ($rsvp_map[$week['week_id']] ?? '') === 'canceled' ? 'selected' : '' ?>>Canceled</option>
              </select>
              <noscript><button type="submit">Submit</button></noscript>
            </form>
          <?php else: ?>
            <span style="color: gray;">RSVP Closed</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php require_once 'private/footer.php'; ?>
