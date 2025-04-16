<!-- Manage Users Section -->
<section>
  <div class="section-header" data-section="manage-users" onclick="toggleSection(this)">
    <h3>Manage Users</h3>
  </div>
  <div class="section-content">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= h($user->first_name . " " . $user->last_name) ?></td>
            <td><?= h($user->email) ?></td>
            <td><?= $user->is_active ? 'Active' : 'Inactive' ?></td>
            <td>
              <a href="toggle_entity.php?id=<?= $user->user_id ?>&action=<?= $user->is_active ? 'deactivate' : 'activate' ?>&type=user">
                <?= $user->is_active ? 'Deactivate User' : 'Activate User' ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Manage Vendors Section -->
<section>
  <div class="section-header" data-section="manage-vendors" onclick="toggleSection(this)">
    <h3>Manage Vendors</h3>
  </div>
  <div class="section-content">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>RSVP Status</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vendor_list as $vendor): ?>
          <tr>
            <td><?= h($vendor->first_name . " " . $vendor->last_name) ?></td>
            <td><?= h($vendor->email) ?></td>
            <td>
              <form method="POST" action="update_rsvp.php" role="form">
                <input type="hidden" name="vendor_id" value="<?= h($vendor->vendor_id) ?>">
                <select name="week_id" required>
                  <option value="">Select a Week</option>
                  <?php if (!empty($market_weeks_map[$vendor->vendor_id])): ?>
                    <?php foreach ($market_weeks_map[$vendor->vendor_id] as $week): ?>
                      <option value="<?= h($week['week_id']) ?>">
                        <?= h($week['week_start'] . " - " . $week['week_end']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="">No Confirmed Weeks</option>
                  <?php endif; ?>
                </select>
                <?php
                $selected_week_id = $market_weeks_map[$vendor->vendor_id][0]['week_id'] ?? null;
                $default_status = 'planned';
                $current_status = $selected_week_id && isset($vendor_rsvp_map[$vendor->vendor_id][$selected_week_id])
                  ? $vendor_rsvp_map[$vendor->vendor_id][$selected_week_id]
                  : $default_status;
                ?>
                <select name="status" required>
                  <option value="planned" <?= $current_status == 'planned' ? 'selected' : '' ?>>Planned</option>
                  <option value="confirmed" <?= $current_status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="canceled" <?= $current_status == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                </select>
                <button type="submit" aria-label="Change Market Status">Update</button>
              </form>
            </td>
            <td><?= $vendor->is_active ? 'Active' : 'Inactive' ?></td>
            <td>
              <?php
              $vendor_action = $vendor->is_active ? 'deactivate' : 'activate';
              $button_class = $vendor->is_active ? 'btn btn-danger' : 'btn btn-success';
              $button_text = $vendor->is_active ? 'Deactivate Vendor' : 'Activate Vendor';
              ?>
              <a href="toggle_entity.php?id=<?= $vendor->user_id ?>&action=<?= $vendor_action ?>&type=vendor" class="<?= $button_class ?>">
                <?= $button_text ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Create Vendor Section -->
<section>
  <div class="section-header" data-section="create-vendor" onclick="toggleSection(this)">
    <h3>Create Vendor</h3>
  </div>

  <div class="section-content">
    <form action="/private/auth.php" method="POST" enctype="multipart/form-data" role="form">
      <input type="hidden" name="admin_created" value="1">
      <input type="hidden" name="is_vendor" value="1">
      <!-- LEFT SIDE FORM -->
      <div>
        <fieldset>
          <input type="text" id="username" name="username" required aria-label="Username" placeholder="Username">
        </fieldset>

        <fieldset>
          <input type="text" id="fname" name="fname" required aria-label="First Name" placeholder="First Name">
        </fieldset>

        <fieldset>
          <input type="text" id="lname" name="lname" required aria-label="Last Name" placeholder="Last Name">
        </fieldset>

        <fieldset>
          <input type="email" id="email" name="email" required aria-label="Email Address" placeholder="Email Address">
        </fieldset>

        <fieldset>
          <input type="password" id="password" name="password" required aria-label="Password" placeholder="Password">
        </fieldset>

        <fieldset>
          <input type="password" id="confirm-pass" name="confirm-pass" required aria-label="Confirm Password" placeholder="Confirm Password">
        </fieldset>

        <fieldset>
          <input type="text" id="business-name" name="business_name" required aria-label="Business Name" placeholder="Business Name">
        </fieldset>

        <fieldset>
          <input type="text" id="contact-number" name="contact_number" required aria-label="Contact Number" placeholder="Contact Number">
        </fieldset>

        <fieldset>
          <input type="text" id="business-ein" name="business_EIN" required aria-label="Business EIN" placeholder="Business EIN">
        </fieldset>
      </div>

      <!-- RIGHT SIDE FORM -->
      <div>
        <fieldset>
          <input type="email" id="business-email" name="business_email" required aria-label="Business Email" placeholder="Business Email">
        </fieldset>

        <fieldset>
          <input type="url" id="website" name="website" aria-label="Website" placeholder="Website (optional)">
        </fieldset>

        <fieldset>
          <input type="text" id="city" name="city" required aria-label="City" placeholder="City">
        </fieldset>

        <fieldset>
          <select id="state" name="state_id" required aria-label="State">
            <option value="">Select State</option>
            <?php
            $state_sql = "SELECT state_id, state_abbr FROM state ORDER BY state_abbr ASC";
            $state_stmt = $db->prepare($state_sql);
            $state_stmt->execute();
            $states = $state_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($states as $state) {
              echo "<option value=\"" . h($state['state_id']) . "\">" . h($state['state_abbr']) . "</option>";
            }
            ?>
          </select>
        </fieldset>

        <fieldset>
          <input type="text" id="street-address" name="street_address" required aria-label="Street Address" placeholder="Street Address">
        </fieldset>

        <fieldset>
          <input type="text" id="zip-code" name="zip_code" required aria-label="Zip Code" placeholder="Zip Code">
        </fieldset>

        <div>
          <fieldset>
            <textarea id="description" name="description" required aria-label="Business Description" placeholder="Business Description, a short blurb about your business."></textarea>
          </fieldset>

          <fieldset>
            <textarea id="vendor-bio" name="vendor_bio" required aria-label="Vendor Bio" placeholder="Vendor Bio, tell the consumer about the history of your business!"></textarea>
          </fieldset>
        </div>
      </div>

      <!-- IMAGE + SUBMIT -->
      <div>
        <fieldset>
          <label for="admin-vendor-photo">Choose Vendor Photo</label>

          <img class="image-preview"
            src="img/assets/add-photo.svg"
            alt="Vendor Profile Preview"
            data-preview="image-preview"
            height="300"
            width="300"
            loading="lazy">

          <input type="file"
            id="admin-vendor-photo"
            name="profile-pic"
            class="image-input"
            data-preview="image-preview"
            accept="image/png, image/jpeg, image/webp"
            onchange="previewImage(event)">
        </fieldset>

        <div>
          <button class="signup-button" type="submit" name="register" value="1">New Vendor</button>
        </div>
      </div>

    </form>
  </div>
</section>

<?php if (SuperAdmin::isSuperAdminLoggedIn()): ?>
  <!-- Manage Admins Section -->
  <section>
    <div class="section-header" data-section="manage-admins" onclick="toggleSection(this)">
      <h3>Manage Admins</h3>
    </div>
    <div class="section-content">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $admin): ?>
            <tr>
              <td><?= h($admin->first_name . " " . $admin->last_name) ?></td>
              <td><?= h($admin->email) ?></td>
              <td>Admin</td>
              <td><?= $admin->is_active ? 'Active' : 'Inactive' ?></td>
              <td>
                <a href="toggle_entity.php?id=<?= h($admin->user_id) ?>&action=<?= $admin->is_active ? 'deactivate' : 'activate' ?>&type=admin"
                  class="btn <?= $admin->is_active ? 'btn-danger' : 'btn-success' ?>">
                  <?= $admin->is_active ? 'Deactivate' : 'Activate' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
    </div>
  </section>
<?php endif; ?>

<!-- Pending Vendor Requests -->
<section>
  <div class="section-header" data-section="pending-approvals" onclick="toggleSection(this)">
    <h3>Pending Vendor Requests</h3>
  </div>
  <div class="section-content">
    <table>
      <thead>
        <tr>
          <th>Vendor Name</th>
          <th>Email</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pending_vendors as $vendor): ?>
          <tr>
            <td>
              <?php $status = $vendor['vendor_status'] ?? ''; ?>

              <?= h($vendor['business_name']) ?>
              <?php if ($status === 'denied'): ?>
                <span class="status-rejected" title="This vendor was rejected but can still be approved later.">(rejected)</span>
              <?php endif; ?>
            </td>
            <td><?= h($vendor['email']) ?></td>
            <td>
              <a href="approve_vendor.php?vendor_id=<?= h($vendor['vendor_id']) ?>&action=approve" class="btn btn-success">Approve</a>
              <a href="approve_vendor.php?vendor_id=<?= h($vendor['vendor_id']) ?>&action=reject" class="btn btn-danger">Reject</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Schedule Market -->
<section>
  <div class="section-header" data-section="schedule-market" onclick="toggleSection(this)">
    <h3>Schedule a Market Week</h3>
  </div>
  <div class="section-content">
    <form action="schedule_market.php" method="POST" role="form">
      <fieldset>
        <label for="week_start">Week Start:</label>
        <input type="date" id="week_start" name="week_start" required>
      </fieldset>
      <fieldset>
        <label for="week_end">Week End:</label>
        <input type="date" id="week_end" name="week_end" required>
      </fieldset>
      <fieldset>
        <label for="confirmation_deadline">RSVP Deadline:</label>
        <input type="date" id="confirmation_deadline" name="confirmation_deadline" required>
      </fieldset>
      <button type="submit">Schedule Market</button>
    </form>
  </div>
</section>

<!-- Manage Market Weeks Section -->
<section>
  <div class="section-header" data-section="manage-markets" onclick="toggleSection(this)">
    <h3>Manage Market Weeks</h3>
  </div>
  <div class="section-content">
    <table>
      <thead>
        <tr>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($upcoming_markets as $week): ?>
          <tr>
            <td><?= h($week['week_start']) ?></td>
            <td><?= h($week['week_end']) ?></td>
            <td><?= isset($week['market_status']) ? h(ucfirst($week['market_status'])) : '<span class="text-muted">No Status</span>' ?></td>

            <td style="display: flex; gap: 0.5rem;">
              <?php if ($week['market_status'] !== 'cancelled'): ?>
                <form method="POST" action="cancel_market.php" onsubmit="return confirm('Cancel this market week?');" role="form">
                  <input type="hidden" name="week_id" value="<?= h($week['week_id']) ?>">
                  <button type="submit" class="btn btn-warning" aria-label="Cancel Market">Cancel</button>
                </form>
              <?php else: ?>
                <span class="text-muted">Cancelled</span>
              <?php endif; ?>

              <form method="POST" action="delete_market.php" onsubmit="return confirm('Permanently delete this market week? This cannot be undone.');" role="form">
                <input type="hidden" name="week_id" value="<?= h($week['week_id']) ?>">
                <button type="submit" class="btn btn-danger" aria-label="Delete Market">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Edit Homepage Message -->
<section>
  <div class="section-header" data-section="update-content" onclick="toggleSection(this)">
    <h3>Edit Homepage Welcome Message</h3>
  </div>
  <div class="section-content">
    <form method="POST" action="update_homepage.php" role="form">
      <textarea name="content" rows="5" cols="50" required><?= h($homepage_content) ?></textarea>
      <input type="hidden" name="section" value="welcome">
      <button type="submit" aria-label="Update Homepage Content">Update Content</button>
    </form>
  </div>
</section>
