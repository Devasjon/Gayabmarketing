<?php
namespace Database\Seeders;
use App\Models\Product;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  $items=[
   ['name_en'=>'Essential Guide to Starting & Managing a Homestay','name_bm'=>'Panduan Asas Memulakan & Mengendalikan Homestay','slug'=>'panduan-homestay','category'=>'Ebook','description_en'=>'A practical guide for Malaysian homestay owners—from planning to daily operations.','description_bm'=>'Panduan praktikal untuk pemilik homestay Malaysia—daripada perancangan hingga operasi.','price_cents'=>3900],
   ['name_en'=>'Ultimate Fantasy & Romantasy Worldbuilding Workbook','name_bm'=>'Ultimate Fantasy & Romantasy Worldbuilding Workbook','slug'=>'worldbuilding-workbook','category'=>'Workbook','description_en'=>'Build living worlds, robust magic systems and unforgettable characters.','description_bm'=>'Bina dunia yang hidup, sistem magis yang kukuh dan watak yang sukar dilupakan.','price_cents'=>4900],
   ['name_en'=>'Dental Locum Commission Calculator','name_bm'=>'Sistem Pengiraan Komisen Lokum Klinik Gigi','slug'=>'komisen-lokum','category'=>'Template','description_en'=>'A smart spreadsheet for treatment records, daily revenue and monthly commissions.','description_bm'=>'Template spreadsheet pintar untuk rekod rawatan, hasil harian dan komisen bulanan.','price_cents'=>5900],
   ['name_en'=>'Kishotenketsu Story Workbook','name_bm'=>'Kishotenketsu Story Workbook','slug'=>'kishotenketsu-workbook','category'=>'Workbook','description_en'=>'Plan a four-part story with a gentle, creative and guided approach.','description_bm'=>'Rangka cerita empat bahagian dengan pendekatan yang lembut, kreatif dan terarah.','price_cents'=>3500],
  ];
  foreach($items as $item) Product::updateOrCreate(['slug'=>$item['slug']],$item+['status'=>'published']);
 }
}

