@extends('layouts.app')
@section('content')
@php
$kickers = [
 'panduan-homestay' => ['sym'=>'⌂','en'=>'BUSINESS GUIDE','bm'=>'PANDUAN BISNES'],
 'worldbuilding-workbook' => ['sym'=>'✦','en'=>'FOR WRITERS','bm'=>'UNTUK PENULIS'],
 'komisen-lokum' => ['sym'=>'+','en'=>'CLINIC MANAGEMENT','bm'=>'PENGURUSAN KLINIK'],
 'kishotenketsu-workbook' => ['sym'=>'四','en'=>'STORY STRUCTURE','bm'=>'STRUKTUR CERITA'],
];
@endphp
<section class="hero">
 <div>
  <p class="eyebrow" data-en="LOCAL DIGITAL PRODUCTS, IDEAS WITHOUT BORDERS" data-bm="PRODUK DIGITAL TEMPATAN, IDEA TANPA SEMPADAN">LOCAL DIGITAL PRODUCTS, IDEAS WITHOUT BORDERS</p>
  <h1 data-en="Practical knowledge. Designed for growth." data-bm="Ilmu praktikal. Direka untuk berkembang.">Practical knowledge.<br><em>Designed for growth.</em></h1>
  <p data-en="Premium ebooks, workbooks and templates in English and Malay—created for entrepreneurs, professionals and writers ready to move forward." data-bm="Ebook, workbook dan template premium dalam Bahasa Melayu & Inggeris—dicipta untuk usahawan, profesional dan penulis yang mahu bergerak lebih jauh.">Premium ebooks, workbooks and templates in English and Malay—created for entrepreneurs, professionals and writers ready to move forward.</p>
  <div class="hero-cta">
   <a class="btn" href="#products" data-en="Explore Products →" data-bm="Terokai Produk →">Explore Products →</a>
   <a class="link" href="#about" data-en="Meet the brand ↘" data-bm="Kenali kami ↘">Meet the brand ↘</a>
  </div>
  <ul class="trust-list">
   <li data-en="✓ Instant download" data-bm="✓ Muat turun segera">✓ Instant download</li>
   <li data-en="✓ Secure payment" data-bm="✓ Pembayaran selamat">✓ Secure payment</li>
   <li data-en="✓ Made in Malaysia" data-bm="✓ Dibuat di Malaysia">✓ Made in Malaysia</li>
  </ul>
 </div>
 <div class="hero-art"><span>WORLDS<br><em>WORTH</em><br>WRITING</span></div>
</section>

<section class="trust-bar">
 <p data-en="Trusted by creators, entrepreneurs and professionals across Malaysia" data-bm="Dipercayai oleh pencipta, usahawan dan profesional di seluruh Malaysia">Trusted by creators, entrepreneurs and professionals across Malaysia</p>
 <div class="trust-chips">
  <span data-en="Entrepreneurs" data-bm="Usahawan">Entrepreneurs</span>
  <span data-en="Homestay Owners" data-bm="Pemilik Homestay">Homestay Owners</span>
  <span data-en="Clinic Professionals" data-bm="Profesional Klinik">Clinic Professionals</span>
  <span data-en="Writers" data-bm="Penulis">Writers</span>
 </div>
</section>

<section id="products" class="products">
 <p class="eyebrow" data-en="FEATURED COLLECTION" data-bm="KOLEKSI PILIHAN">FEATURED COLLECTION</p>
 <h2 data-en="Tools that turn ideas into reality." data-bm="Alat untuk menjadikan idea satu kenyataan.">Tools that turn <em>ideas into reality.</em></h2>
 <p class="section-lead" data-en="Every product combines clear steps, thoughtful design and context relevant to Malaysian users." data-bm="Setiap produk dibina dengan langkah yang jelas, reka bentuk yang kemas dan konteks yang dekat dengan pengguna Malaysia.">Every product combines clear steps, thoughtful design and context relevant to Malaysian users.</p>

 @if($products->isEmpty())
 <div class="coming"><b>COMING SOON</b><h3 data-en="Our first collection is being prepared." data-bm="Koleksi pertama kami sedang disediakan.">Our first collection is being prepared.</h3><p data-en="Join our mailing list to be notified when the products are ready." data-bm="Sertai senarai e-mel untuk menerima notis apabila produk siap.">Join our mailing list to be notified when the products are ready.</p></div>
 @else
 <div class="filters" role="group" aria-label="Filter products by category">
  <button class="filter-btn is-active" data-filter="all" data-en="All" data-bm="Semua">All</button>
  <button class="filter-btn" data-filter="Ebook" data-en="Ebook" data-bm="Ebook">Ebook</button>
  <button class="filter-btn" data-filter="Workbook" data-en="Workbook" data-bm="Workbook">Workbook</button>
  <button class="filter-btn" data-filter="Template" data-en="Template" data-bm="Template">Template</button>
 </div>
 <div class="grid">
  @foreach($products as $product)
  @php $kicker = $kickers[$product->slug] ?? ['sym'=>'✦','en'=>strtoupper($product->category),'bm'=>strtoupper($product->category)]; @endphp
  <article data-category="{{ $product->category }}">
   <div class="cover">
    <span>{{ $kicker['sym'] }}</span>
    <strong>{{ $product->name_en }}</strong>
   </div>
   <p class="kicker" data-en="{{ $kicker['en'] }}" data-bm="{{ $kicker['bm'] }}">{{ $kicker['en'] }}</p>
   <p class="badge">{{ strtoupper($product->category) }}</p>
   <h3 data-en="{{ $product->name_en }}" data-bm="{{ $product->name_bm }}">{{ $product->name_en }}</h3>
   <p data-en="{{ $product->description_en }}" data-bm="{{ $product->description_bm }}">{{ $product->description_en }}</p>
   <b>RM{{ number_format($product->price_cents/100,2) }}</b>
   <a href="{{ route('products.show',$product) }}" data-en="View →" data-bm="Lihat →">View →</a>
  </article>
  @endforeach
 </div>
 <a class="view-all" href="{{ route('home') }}#products" data-en="View all products →" data-bm="Lihat semua produk →">View all products →</a>
 @endif
</section>

<section id="publishing" class="publishing">
 <p class="eyebrow" data-en="GAYA B PUBLISHING" data-bm="GAYA B PUBLISHING">GAYA B PUBLISHING</p>
 <h2 data-en="From manuscript to meaningful work." data-bm="Daripada manuskrip kepada karya bermakna.">From manuscript to <em>meaningful work.</em></h2>
 <p data-en="We develop books and learning materials that are beautiful, clear, useful and enjoyable to read." data-bm="Kami membangunkan buku dan bahan pembelajaran yang bukan sahaja cantik dipandang, tetapi jelas, berguna dan menyenangkan untuk dibaca.">We develop books and learning materials that are beautiful, clear, useful and enjoyable to read.</p>
 <ol class="steps">
  <li data-en="Content development & editorial" data-bm="Pembangunan kandungan & editorial">Content development & editorial</li>
  <li data-en="Ebook & workbook design" data-bm="Reka bentuk ebook & workbook">Ebook & workbook design</li>
  <li data-en="Digital publishing for global markets" data-bm="Penerbitan digital untuk pasaran global">Digital publishing for global markets</li>
 </ol>
 <a class="link" href="mailto:admin@gayabmarketing.com" data-en="Discuss your project →" data-bm="Bincang projek anda →">Discuss your project →</a>
</section>

<section id="free" class="free-resources">
 <p class="eyebrow" data-en="START FOR FREE" data-bm="MULA SECARA PERCUMA">START FOR FREE</p>
 <h2 data-en="One small step can open a bigger path." data-bm="Satu langkah kecil boleh membuka jalan besar.">One small step <em>can open a bigger path.</em></h2>
 <p data-en="Download the “7 Steps to Turn an Idea into a Digital Product” checklist and start your first idea today." data-bm="Muat turun checklist ringkas “7 Langkah Menukar Idea Menjadi Produk Digital” dan mulakan idea pertama anda hari ini.">Download the "7 Steps to Turn an Idea into a Digital Product" checklist and start your first idea today.</p>
 <a class="btn" href="mailto:admin@gayabmarketing.com?subject=Free%20Resource%20-%20Gaya%20B" data-en="Get the free checklist →" data-bm="Dapatkan checklist percuma →">Get the free checklist →</a>
 <ol class="steps">
  <li data-en="Identify the problem" data-bm="Kenal pasti masalah">Identify the problem</li>
  <li data-en="Define the buyer" data-bm="Tentukan pembeli">Define the buyer</li>
  <li data-en="Build the solution" data-bm="Bina penyelesaian">Build the solution</li>
 </ol>
</section>

<section id="about" class="about">
 <p class="eyebrow" data-en="OUR STORY" data-bm="CERITA KAMI">OUR STORY</p>
 <h2 data-en="Local insight, professional standards." data-bm="Ilmu tempatan, standard profesional.">Local insight, <em>professional standards.</em></h2>
 <p data-en="Gaya B Marketing is a digital-product and publishing business from Borneo, Malaysia. We believe good knowledge should be easy to use—not merely read." data-bm="Gaya B Marketing ialah perniagaan produk digital dan penerbitan dari Borneo, Malaysia. Kami percaya ilmu yang baik perlu mudah digunakan—bukan sekadar dibaca.">Gaya B Marketing is a digital-product and publishing business from Borneo, Malaysia. We believe good knowledge should be easy to use—not merely read.</p>
 <p data-en="Every product is thoughtfully planned to help customers save time, organise ideas and act with greater confidence." data-bm="Setiap produk dirancang dengan teliti untuk membantu pelanggan menjimatkan masa, menyusun idea dan mengambil tindakan dengan lebih yakin.">Every product is thoughtfully planned to help customers save time, organise ideas and act with greater confidence.</p>
 <div class="stats">
  <div><b>2</b><span data-en="Languages" data-bm="Bahasa">Languages</span></div>
  <div><b>100%</b><span data-en="Digital" data-bm="Digital">Digital</span></div>
  <div><b>MY</b><span data-en="Created in Malaysia" data-bm="Dicipta di Malaysia">Created in Malaysia</span></div>
 </div>
</section>

<section id="faq" class="faq">
 <p class="eyebrow" data-en="FREQUENTLY ASKED QUESTIONS" data-bm="SOALAN LAZIM">FREQUENTLY ASKED QUESTIONS</p>
 <h2 data-en="Have questions? We have answers." data-bm="Ada soalan? Kami ada jawapan.">Have questions? <em>We have answers.</em></h2>
 <a class="link" href="mailto:admin@gayabmarketing.com" data-en="Contact us →" data-bm="Hubungi kami →">Contact us →</a>
 <div class="faq-list">
  <article class="faq-item is-open">
   <button aria-expanded="true" data-en="How will I receive my files after purchase?" data-bm="Bagaimana saya menerima fail selepas pembelian?">How will I receive my files after purchase?<span>−</span></button>
   <p data-en="A download link will be emailed automatically after successful payment." data-bm="Pautan muat turun akan dihantar secara automatik ke e-mel anda selepas pembayaran berjaya.">A download link will be emailed automatically after successful payment.</p>
  </article>
  <article class="faq-item">
   <button aria-expanded="false" data-en="Can the products be printed?" data-bm="Adakah produk boleh dicetak?">Can the products be printed?<span>+</span></button>
   <p data-en="Yes—all files are provided as print-ready PDFs for personal use." data-bm="Ya—semua fail disediakan dalam format PDF sedia cetak untuk kegunaan peribadi.">Yes—all files are provided as print-ready PDFs for personal use.</p>
  </article>
  <article class="faq-item">
   <button aria-expanded="false" data-en="May I resell purchased files?" data-bm="Bolehkah saya menjual semula fail yang dibeli?">May I resell purchased files?<span>+</span></button>
   <p data-en="No—files are licensed for personal or single-business use only and may not be resold or redistributed." data-bm="Tidak—fail dilesenkan untuk kegunaan peribadi atau satu perniagaan sahaja dan tidak boleh dijual semula atau diedarkan.">No—files are licensed for personal or single-business use only and may not be resold or redistributed.</p>
  </article>
 </div>
</section>

<section class="newsletter">
 <p class="eyebrow" data-en="LETTERS FROM BORNEO" data-bm="SURAT DARI BORNEO">LETTERS FROM BORNEO</p>
 <h2 data-en="Ideas, inspiration & new products—straight to your inbox." data-bm="Idea, inspirasi & produk baharu—terus ke peti masuk anda.">Ideas, inspiration & new products—<em>straight to your inbox.</em></h2>
 <form id="newsletter-form">
  <input type="email" required placeholder="Alamat e-mel anda" data-en-placeholder="Your email address" data-bm-placeholder="Alamat e-mel anda">
  <button type="submit" data-en="Subscribe →" data-bm="Langgan →">Subscribe →</button>
 </form>
 <p class="newsletter-note" data-en="No spam. Only useful ideas." data-bm="Tiada spam. Hanya perkongsian yang berguna.">No spam. Only useful ideas.</p>
</section>
@endsection
