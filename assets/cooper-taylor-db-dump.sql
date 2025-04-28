CREATE DATABASE farmers_market;
USE farmers_market;

CREATE TABLE user_level (
    user_level_id INT PRIMARY KEY AUTO_INCREMENT,
    user_level_name ENUM('member', 'vendor', 'admin', 'super_admin') NOT NULL,
    description VARCHAR(255)
);

INSERT INTO user_level (user_level_id, user_level_name, description) VALUES
(1, 'member', 'Basic market member with limited privileges.'),
(2, 'vendor', 'Market vendor with ability to list products and attend markets.'),
(3, 'admin', 'Administrator with the ability to manage vendor and member accounts.'),
(4, 'super_admin', 'Super admin with full system control.');

CREATE TABLE state (
    state_id INT PRIMARY KEY AUTO_INCREMENT,
    state_abbr VARCHAR(2) UNIQUE NOT NULL,
    state_name VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO state (state_id, state_name, state_abbr) VALUES
(1, 'North Carolina', 'NC'),
(2, 'South Carolina', 'SC'),
(3, 'Virginia', 'VA'),
(4, 'Tennessee', 'TN'),
(5, 'Georgia', 'GA');

CREATE TABLE category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name ENUM(
      'fruit',
      'vegetable',
      'bread',
      'meat',
      'eggs',
      'cheese',
      'dairy',
      'honey',
      'herbs',
      'flowers',
      'plants',
      'jams',
      'sauces',
      'baked_goods',
      'pastries',
      'drinks',
      'crafts',
      'soaps',
      'prepared_food',
      'other'
    ) NOT NULL
);

-- Insert data into category
INSERT INTO category (category_id, category_name) VALUES
(1, 'fruit'),
(2, 'vegetable'),
(3, 'bread'),
(4, 'meat'),
(5, 'eggs'),
(6, 'cheese'),
(7, 'dairy'),
(8, 'honey'),
(9, 'herbs'),
(10, 'flowers'),
(11, 'plants'),
(12, 'jams'),
(13, 'sauces'),
(14, 'baked_goods'),
(15, 'pastries'),
(16, 'drinks'),
(17, 'crafts'),
(18, 'soaps'),
(19, 'prepared_food'),
(20, 'other');

CREATE TABLE amount_offered (
  amount_id INT PRIMARY KEY AUTO_INCREMENT,
  amount_name ENUM(
    'individual',
    'bunch',
    'bag',
    'box',
    'pack',
    'bundle',
    'piece',
    'head',
    'dozen',
    'half dozen',
    'lbs',
    '1/2lbs',
    '1/4lbs',
    '2lbs',
    '5lbs',
    '10lbs',
    'bushel',
    '1/2 bushel',
    'quart',
    'pint',
    'gallon',
    'oz',
    '16oz',
    '32oz',
    'jar',
    'bottle',
    'container'
  ),
  description TEXT
);

INSERT INTO amount_offered (amount_id, amount_name, description) VALUES
(1, 'individual', 'Single item.'),
(2, 'bunch', 'Grouped quantity of items.'),
(3, 'bag', 'Bag of goods.'),
(4, 'box', 'Boxed packaging.'),
(5, 'pack', 'Packaged in a pack.'),
(6, 'bundle', 'Bound collection of items.'),
(7, 'piece', 'One item/piece.'),
(8, 'head', 'Head (e.g., lettuce).'),
(9, 'dozen', '12 items.'),
(10, 'half dozen', '6 items.'),
(11, 'lbs', 'Measured in pounds.'),
(12, '1/2lbs', 'Half-pound measurement.'),
(13, '1/4lbs', 'Quarter-pound measurement.'),
(14, '2lbs', 'Two pounds.'),
(15, '5lbs', 'Five pounds.'),
(16, '10lbs', 'Ten pounds.'),
(17, 'bushel', 'Full bushel size.'),
(18, '1/2 bushel', 'Half bushel size.'),
(19, 'quart', 'Quarter gallon measurement.'),
(20, 'pint', 'Half of a quart.'),
(21, 'gallon', 'Full gallon.'),
(22, 'oz', 'Ounces.'),
(23, '16oz', 'Sixteen ounces.'),
(24, '32oz', 'Thirty-two ounces.'),
(25, 'jar', 'Packaged in a jar.'),
(26, 'bottle', 'Packaged in a bottle.'),
(27, 'container', 'Packaged in a container.');

CREATE TABLE market_week (
  week_id INT AUTO_INCREMENT PRIMARY KEY,
  week_start DATE UNIQUE NOT NULL,
  week_end DATE NOT NULL,
  confirmation_deadline DATE NOT NULL,
  market_status ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed',
  is_deleted tinyint(1) NOT NULL DEFAULT 0
);

CREATE TABLE homepage_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section VARCHAR(50) NOT NULL UNIQUE,
  content TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE product_tag (
    tag_id INT PRIMARY KEY AUTO_INCREMENT,
    tag_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    user_level_id INT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    FOREIGN KEY (user_level_id) REFERENCES user_level(user_level_id)
);

CREATE TABLE vendor (
    vendor_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    business_name VARCHAR(50) NOT NULL,
    contact_number VARCHAR(20),
    business_EIN VARCHAR(50),
    business_email VARCHAR(255),
    website VARCHAR(255),
    city VARCHAR(50),
    state_id INT,
    street_address VARCHAR(255),
    zip_code VARCHAR(10),
    description TEXT,
    vendor_bio TEXT,
    vendor_status ENUM('pending', 'approved', 'denied') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (state_id) REFERENCES state(state_id) ON DELETE CASCADE
);

CREATE TABLE profile_image (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(8,2),
    amount_id INT,
    category_id INT,
    description VARCHAR(255),
    FOREIGN KEY (vendor_id) REFERENCES vendor(vendor_id),
    FOREIGN KEY (amount_id) REFERENCES amount_offered(amount_id),
    FOREIGN KEY (category_id) REFERENCES category(category_id)
);


CREATE TABLE product_tag_map (
  product_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (product_id, tag_id),
  FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES product_tag(tag_id)
);

CREATE TABLE product_image (
  image_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNIQUE NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE
);


CREATE TABLE vendor_market (
    market_vendor_id INT AUTO_INCREMENT PRIMARY KEY,
    week_id INT NOT NULL,
    vendor_id INT NOT NULL,
    status ENUM('planned', 'confirmed', 'canceled') NOT NULL DEFAULT 'planned',
    FOREIGN KEY (week_id) REFERENCES market_week(week_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendor(vendor_id) ON DELETE CASCADE
);

CREATE TABLE favorite (
    user_id INT NOT NULL,
    vendor_id INT NOT NULL,
    PRIMARY KEY (user_id, vendor_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendor(vendor_id) ON DELETE CASCADE
);
