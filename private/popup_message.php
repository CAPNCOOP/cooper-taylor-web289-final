<?php
if (isset($_GET['message'])): ?>
  <div class="feedback-popup">
    <?php
    switch ($_GET['message']) {
      // General Messages
      case 'profile_updated':
        echo "✅ Profile updated successfully!";
        break;
      case 'error_update_failed':
        echo "❌ Error: Profile update failed.";
        break;
      case 'error_invalid_image':
        echo "❌ Error: Invalid image format.";
        break;

      // Product Management
      case 'product_added':
        echo "✅ Product added successfully!";
        break;
      case 'product_updated':
        echo "✅ Product updated successfully!";
        break;
      case 'product_deleted':
        echo "✅ Product deleted successfully!";
        break;
      case 'error_empty_fields':
        echo "❌ Error: All fields must be filled.";
        break;
      case 'error_invalid_product':
        echo "❌ Error: Invalid product selected.";
        break;
      case 'error_unauthorized':
        echo "❌ Error: You do not have permission to edit this product.";
        break;
      case 'error_upload':
        echo "❌ Error: Image upload failed.";
        break;
      case 'warning_no_changes':
        echo "⚠️ Warning: No changes were made.";
        break;

      // Vendor Management
      case 'vendor_approval_pending':
        echo "🚫 Your vendor account is pending approval. Check back later.";
        break;
      case 'vendor_denied':
        echo "❌ Your vendor application was denied. Please contact support.";
        break;

      // Favorite Vendor
      case 'favorite_added':
        echo "✅ Vendor added to favorites!";
        break;
      case 'favorite_removed':
        echo "✅ Vendor removed from favorites!";
        break;
      case 'error_invalid_vendor':
        echo "❌ Error: Invalid vendor selected.";
        break;
      case 'error_not_logged_in':
        echo "❌ Error: You must be logged in to favorite a vendor.";
        break;
      case 'error_add_failed':
        echo "❌ Error: Failed to add vendor to favorites.";
        break;
      case 'error_remove_failed':
        echo "❌ Error: Failed to remove vendor from favorites.";
        break;

      // RSVP Actions 
      case 'rsvp_updated':
        echo "✅ RSVP updated successfully!";
        break;
      case 'rsvp_submitted':
        echo "✅ RSVP submitted successfully!";
        break;
      case 'error_missing_fields':
        echo "❌ Error: Missing market week or RSVP status.";
        break;
      case 'error_vendor_not_approved':
        echo "❌ Error: You must be an approved vendor to RSVP.";
        break;

      // Login / Authentication
      case 'invalid_credentials':
        echo "❌ Invalid username or password.";
        break;
      case 'account_inactive':
        echo "❌ Your account has been deactivated. Contact support.";
        break;
      case 'signup_success':
        echo "✅ Account created successfully! Pending approval if you registered as a vendor.";
        break;
      case 'logout_success':
        $name = htmlspecialchars($_GET['name'] ?? 'User');
        echo "✅ Goodbye, $name! You have been logged out.";
        break;
      default:
        echo "⚠️ Unknown message.";
        break;
    }
    ?>
  </div>
<?php endif; ?>
