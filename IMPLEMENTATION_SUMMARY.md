# Dynamic Item Management Implementation Summary

## Changes Made

### 1. Database Structure
- **Created `tblitem` table** for dynamic item management
- **Updated `order_items` table** to include `items_id` column
- Created migration scripts in `/database/` folder

### 2. Frontend Changes (`Canteen Staff/create_orders.php`)
- **Dynamic item fetching**: Items are now fetched from `tblitem` table instead of static HTML options
- **Updated form field**: Changed from `description[]` to `items_id[]`
- **JavaScript updates**: Updated event listeners to handle `items_id` class instead of `description`
- **Price display**: Price per unit is now dynamically populated from `item_unit_price`

### 3. Backend Changes
- **Process handler** (`php/processes.php`): Updated to handle `items_id` instead of `description`
- **Database class** (`php/classes.php`): Updated `createOrder()` method to work with item IDs
- **Order insertion**: Modified to use `items_id` in `order_items` table

## Files Modified

1. `Canteen Staff/create_orders.php` - Frontend form and JavaScript
2. `php/processes.php` - Order processing logic
3. `php/classes.php` - Database operations
4. `database/create_tblitem.sql` - New table creation script
5. `database/update_order_items.sql` - Table update script
6. `database/run_migrations.php` - Migration execution script

## Database Schema

### tblitem Table
```sql
CREATE TABLE `tblitem` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `item_unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`item_id`)
);
```

### Updated order_items Table
```sql
ALTER TABLE `order_items` ADD COLUMN `items_id` int(11) DEFAULT NULL AFTER `order_id`;
```

## Initial Data
The system includes initial items matching the previous static options:
- AM Snacks - ₱80.00
- PM Snacks - ₱80.00  
- Snacks - ₱70.00
- Breakfast - ₱90.00
- Lunch - ₱100.00
- Dinner - ₱120.00

## Implementation Steps

1. **Run Database Migrations**:
   ```bash
   cd database
   php run_migrations.php
   ```

2. **Verify Items Table**:
   - Check that `tblitem` table exists and contains initial data
   - Verify `order_items` table has `items_id` column

3. **Test the System**:
   - Go to Create Order page
   - Verify items are loaded dynamically
   - Test price calculation and order submission

## Benefits

✅ **Dynamic Item Management**: Items can now be added/updated through admin interface
✅ **Price Consistency**: Prices are centralized in the database
✅ **Scalability**: Easy to add new food items without code changes
✅ **Data Integrity**: Foreign key relationships ensure data consistency

## Notes

- The `description` and `price` columns in `order_items` are kept for backward compatibility
- Once all existing orders are migrated, these columns can be safely dropped
- The system maintains the same user experience while adding dynamic capabilities
