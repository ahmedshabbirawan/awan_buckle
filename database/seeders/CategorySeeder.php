<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){



        $parent_query = "INSERT INTO `parent_category` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (1, 'Consumable Asset', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (2, 'Physical Asset', 1, NULL, NULL, NULL, NULL, NULL, NULL)";
        \DB::statement($parent_query);


        $sub_query = "INSERT INTO `sub_category` (`id`, `parentCategoryId`, `name`, `is_tagable`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (1, 1, 'Books & Liabraries', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (2, 1, 'Genetorial Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (3, 1, 'Hardware Accessories ', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (4, 1, 'Kitchen Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (5, 1, 'Network Active and Passive Equipment', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (6, 1, 'Printing & Publication Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (7, 1, 'Refreshment Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (8, 1, 'Repair & Maintenance / Miscellaneous / Electrical / Mechanical & Civil Work Material', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (9, 1, 'Software & Licenses (Equipment / Hardware)', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (10, 1, 'Stationery & Computer Sationery Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (11, 1, 'Toners / Fax Rolls', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (12, 2, 'Appliances', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (13, 2, 'Equipment / Hardware Items', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (14, 2, 'Furniture', 'y', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (15, 2, 'Network Active and Passive Equipment', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (16, 2, 'Vehicles', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL)";
        \DB::statement($sub_query);



        $product_query = "INSERT INTO `product_category` (`id`, `parentCategoryId`, `subCategoryId`, `name`, `enable_alert`, `sn_require`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (4, 1, 1, 'Books', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (5, 1, 2, 'Cleaning Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (6, 1, 3, 'Accessories ', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (7, 1, 4, 'Crockery & Cutlery', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (8, 1, 5, 'Networking Tools', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (9, 1, 6, 'Printing Items ', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (10, 1, 7, 'Entertainmnet', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (11, 1, 8, 'Disposable Product', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (12, 1, 8, 'Medical Equipment', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (13, 1, 8, 'R&M / Misc. / Elect. / Mech & Civil Work Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (14, 1, 8, 'Uniform', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (15, 1, 9, 'Software & License', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (16, 1, 10, 'Cleaning Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (17, 1, 10, 'Stationery Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (18, 1, 11, 'Printer & Photocopier Toners /Fax Rolls', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (19, 2, 12, 'Air Curtain', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (20, 2, 12, 'Coffee Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (21, 2, 12, 'Cooler', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (22, 2, 12, 'Electric Geyser', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (23, 2, 12, 'Electric Heater', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (24, 2, 12, 'Electric Hot Plate', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (25, 2, 12, 'Electric Kettles', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (26, 2, 12, 'Electric Water Boiler', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (27, 2, 12, 'Fan', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (28, 2, 12, 'Fridge / Deep Freezer', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (29, 2, 12, 'Microwave Oven', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (30, 2, 12, 'Paper Shredder Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (31, 2, 12, 'Split & Floor Standing Air Conditioner', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (32, 2, 12, 'Vacuum Cleaner', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (33, 2, 12, 'Water Dispenser ', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (34, 2, 13, 'Audio Video Conference System', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (35, 2, 13, 'Automatic Shoe Cover Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (36, 2, 13, 'Biometric Device', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (37, 2, 13, 'Biometric Enable Tablet', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (38, 2, 13, 'Cellular Services', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (39, 2, 13, 'Chassis', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (40, 2, 13, 'Desktop', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (41, 2, 13, 'Dock', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (42, 2, 13, 'Door Bell', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (43, 2, 13, 'Fax Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (44, 2, 13, 'Firewall', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (45, 2, 13, 'Generator', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (46, 2, 13, 'Hard Disk / RAM / Flash Drive', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (47, 2, 13, 'Interactive White Board', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (48, 2, 13, 'Internet Device', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (49, 2, 13, 'IP-Phones', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (50, 2, 13, 'Laptop', 1, 1, 1, NULL, '2022-12-12 11:34:37', NULL, NULL, NULL, NULL),
        (51, 2, 13, 'LCD/LED Screen', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (52, 2, 13, 'Multimedia', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (53, 2, 13, 'Networking Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (54, 2, 13, 'Photocopier Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (55, 2, 13, 'Printers / Thermal Printers', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (56, 2, 13, 'Projector with Screen', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (57, 2, 13, 'RFID Barrier System', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (58, 2, 13, 'SAN Storage / SAN Switch / SAN SSD', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (59, 2, 13, 'Scanner / Barcode Reader / QR Reader', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (60, 2, 13, 'Security System', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (61, 2, 13, 'Server', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (62, 2, 13, 'Smart Phones', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (63, 2, 13, 'Tablet PC', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (64, 2, 13, 'Tape Library', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (65, 2, 13, 'Ticket Dispenser Machine', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (66, 2, 13, 'Tracking Device', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (67, 2, 13, 'UPS', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (68, 2, 14, 'Bean Bags', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (69, 2, 14, 'Cabinet / Cupboard', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (70, 2, 14, 'Chair', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (71, 2, 14, 'Credenza', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (72, 2, 14, 'Drawers', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (73, 2, 14, 'Footrest', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (74, 2, 14, 'Hanger', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (75, 2, 14, 'Notice Board', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (76, 2, 14, 'Rack', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (77, 2, 14, 'Rostrum', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (78, 2, 14, 'Sofa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (79, 2, 14, 'Stand', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (80, 2, 14, 'Table', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (81, 2, 14, 'Trolley', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (82, 2, 14, 'Workstation / Desk', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (83, 2, 15, 'Networking Items', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (84, 2, 15, 'Passive Network Components', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (85, 2, 16, 'Car', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (86, 2, 16, 'MotorCycle', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
        (87, 2, 16, 'Van', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL)";


        \DB::statement($product_query);

    }
}
