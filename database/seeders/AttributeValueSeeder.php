<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){


        

        $commonArr = array('status' => '1', 'created_at' => '2022-01-01 00:00:01');

        /*

        \DB::table('attribute_values')->insert(array (


            // 1. Ram          
            array_merge(array('id' => 1 ,  'type_id' => 1 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 2 ,  'type_id' => 1 ,  'value' => '04 GB DDR2'), $commonArr), 
            array_merge(array('id' => 3 ,  'type_id' => 1 ,  'value' => '04 GB DDR3'), $commonArr), 
            array_merge(array('id' => 4 ,  'type_id' => 1 ,  'value' => '04 GB DDR4'), $commonArr), 
            array_merge(array('id' => 5 ,  'type_id' => 1 ,  'value' => '08 GB DDR2'), $commonArr), 
            array_merge(array('id' => 6 ,  'type_id' => 1 ,  'value' => '08 GB DDR3'), $commonArr), 
            array_merge(array('id' => 7 ,  'type_id' => 1 ,  'value' => '08 GB DDR4'), $commonArr), 
            array_merge(array('id' => 8 ,  'type_id' => 1 ,  'value' => '16 GB DDR2'), $commonArr), 
            array_merge(array('id' => 9 ,  'type_id' => 1 ,  'value' => '16 GB DDR3'), $commonArr), 
            array_merge(array('id' => 10 ,  'type_id' => 1 ,  'value' => '16 GB DDR4'), $commonArr), 
                    
            // 2. HardDisk        
            array_merge(array('id' => 11 ,  'type_id' => 2 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 12 ,  'type_id' => 2 ,  'value' => '320 GB HDD'), $commonArr), 
            array_merge(array('id' => 13 ,  'type_id' => 2 ,  'value' => '500 GB HDD'), $commonArr), 
            array_merge(array('id' => 14 ,  'type_id' => 2 ,  'value' => '1TB HDD'), $commonArr), 
            array_merge(array('id' => 15 ,  'type_id' => 2 ,  'value' => '128 GB SSD'), $commonArr), 
            array_merge(array('id' => 16 ,  'type_id' => 2 ,  'value' => '256 GB SSD'), $commonArr), 
            array_merge(array('id' => 17 ,  'type_id' => 2 ,  'value' => '512 GB SSD'), $commonArr), 
                    
            // 4. Laptop Screen       
            array_merge(array('id' => 18 ,  'type_id' => 4 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 19 ,  'type_id' => 4 ,  'value' => '14" FHD'), $commonArr), 
            array_merge(array('id' => 20 ,  'type_id' => 4 ,  'value' => '15.6 FHD'), $commonArr), 
                    
            // 5. Printer / Scanner T ype         
            array_merge(array('id' => 21 ,  'type_id' => 5 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 22 ,  'type_id' => 5 ,  'value' => 'Light Duty'), $commonArr), 
            array_merge(array('id' => 23 ,  'type_id' => 5 ,  'value' => 'Medium Duty'), $commonArr), 
            array_merge(array('id' => 24 ,  'type_id' => 5 ,  'value' => 'Heavy Duty'), $commonArr), 
                    
            // 6. Duplex         
            array_merge(array('id' => 25 ,  'type_id' => 6 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 26 ,  'type_id' => 6 ,  'value' => 'Yes'), $commonArr), 
            array_merge(array('id' => 27 ,  'type_id' => 6 ,  'value' => 'No'), $commonArr), 
                    
            // 7. Duplex          
            array_merge(array('id' => 28 ,  'type_id' => 7 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 29 ,  'type_id' => 7 ,  'value' => 'B/W'), $commonArr), 
            array_merge(array('id' => 30 ,  'type_id' => 7 ,  'value' => 'Color'), $commonArr), 
                    
            // 8. Print / Scanner Spe ed         
            array_merge(array('id' => 31 ,  'type_id' => 8 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 32 ,  'type_id' => 8 ,  'value' => '21PPM'), $commonArr), 
            array_merge(array('id' => 33 ,  'type_id' => 8 ,  'value' => '38PPM'), $commonArr), 
            array_merge(array('id' => 34 ,  'type_id' => 8 ,  'value' => '40PPM'), $commonArr), 
            array_merge(array('id' => 35 ,  'type_id' => 8 ,  'value' => '22PPM'), $commonArr), 
            array_merge(array('id' => 36 ,  'type_id' => 8 ,  'value' => '35PPM'), $commonArr), 
            array_merge(array('id' => 37 ,  'type_id' => 8 ,  'value' => '25PPM'), $commonArr), 
            array_merge(array('id' => 38 ,  'type_id' => 8 ,  'value' => '30PPM'), $commonArr), 
                    
            // 9. Print / Scanner Res olution        
            array_merge(array('id' => 39 ,  'type_id' => 9 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 40 ,  'type_id' => 9 ,  'value' => '600 * 600 dpi'), $commonArr), 
            array_merge(array('id' => 41 ,  'type_id' => 9 ,  'value' => '1200 * 1200 dpi'), $commonArr), 
                    
            // 10. Scanning Mode        
            array_merge(array('id' => 42 ,  'type_id' => 10 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 43 ,  'type_id' => 10 ,  'value' => 'B/W'), $commonArr), 
            array_merge(array('id' => 44 ,  'type_id' => 10 ,  'value' => 'Color'), $commonArr), 
                    
            // 11. Length         
            array_merge(array('id' => 45 ,  'type_id' => 11 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 46 ,  'type_id' => 11 ,  'value' => '36``'), $commonArr), 
            array_merge(array('id' => 47 ,  'type_id' => 11 ,  'value' => '2`-0``'), $commonArr), 
            array_merge(array('id' => 48 ,  'type_id' => 11 ,  'value' => '3`-0``'), $commonArr), 
            array_merge(array('id' => 49 ,  'type_id' => 11 ,  'value' => '6`-0``'), $commonArr), 
            array_merge(array('id' => 50 ,  'type_id' => 11 ,  'value' => '20`-0``'), $commonArr), 
                    
            // 12. Height          
            array_merge(array('id' => 51 ,  'type_id' => 12 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 52 ,  'type_id' => 12 ,  'value' => '2`-6'), $commonArr), 
                    
            // 13. Width         
            array_merge(array('id' => 53 ,  'type_id' => 13 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 54 ,  'type_id' => 13 ,  'value' => '13`-0'), $commonArr), 
            array_merge(array('id' => 55 ,  'type_id' => 13 ,  'value' => '4`-6'), $commonArr), 
            array_merge(array('id' => 56 ,  'type_id' => 13 ,  'value' => '5`-0``'), $commonArr), 
            array_merge(array('id' => 57 ,  'type_id' => 13 ,  'value' => '4`-0``'), $commonArr), 
            array_merge(array('id' => 58 ,  'type_id' => 13 ,  'value' => '6`-0``'), $commonArr), 
                    
            // 14. Depth          
            array_merge(array('id' => 59 ,  'type_id' => 14 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 60 ,  'type_id' => 14 ,  'value' => '3`-6``'), $commonArr), 
            array_merge(array('id' => 61 ,  'type_id' => 14 ,  'value' => '2`-0``'), $commonArr), 
            array_merge(array('id' => 62 ,  'type_id' => 14 ,  'value' => '1`-4``'), $commonArr), 

            // 15. Furniture Structur e         
            array_merge(array('id' => 63 ,  'type_id' => 15 ,  'value' => 'n/a'), $commonArr), 
            array_merge(array('id' => 64 ,  'type_id' => 15 ,  'value' => '32mm Laminated Board'), $commonArr), 
            array_merge(array('id' => 65 ,  'type_id' => 15 ,  'value' => '16mm Laminated Board'), $commonArr), 
            array_merge(array('id' => 66 ,  'type_id' => 15 ,  'value' => 'Stainless Steel'), $commonArr), 
            array_merge(array('id' => 67 ,  'type_id' => 15 ,  'value' => 'Good Quality Foam'), $commonArr), 

            // 16-3 Processor Generation  
            array_merge(array('id' => 68 ,  'type_id' => 3 ,  'value' => 'n/a'), $commonArr), 
                         
            
        ));

        */
        
    }
}
