@aware(['page', 'unit'])
@props(['product_list'])
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/own/global.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/own/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/own/product-page-specs.css') }}" />
  <title>Document</title>
  <link
  rel="stylesheet"
  href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
/>
<!-- Remix Icon CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css"
  rel="stylesheet"
/>
<!-- Title -->
  {{-- @vite('resources/css/app.css') --}}
</head>
 <body>
    <main>
      <section id="Content1">
        <div class="specs-header">
          <div class="c1-buttons">
            <a href="{{  route('home') }}"
              ><i class="ri-arrow-left-s-fill"></i>Go Back</a
            >
          </div>
        </div>
        <div class="product-specs">
          <div class="home-product-col">
            <img src="{{ asset('storage/'.$unit->image_file ) }}" alt="" />
          </div>
          <div class="heading">
            <h1 class="Title">{{ $unit->model_name }}</h1>
            <h3 class="Price">{{ $unit->price }}</h3>
          </div>u
          <div class="description">
            <h3>Description</h3>
            <p class="description-content">
              {{$unit->description}}. <br />
              <br />
              *Actual Unit May Vary.
            </p>
            {{-- colors [JSON] --}}
            <h5>Colors Available</h5>
            <div class="colors">
              <div class="color1" title="Candy Jazz Blue"></div>
              <div class="color2" title="Vibrant Orange"></div>
              <div class="color3" title="Sports Red"></div>
            </div>
            <h3>Specification</h3>
            <div class="container">
              <div class="specs-content">
                <div class="specs-title">Body Type</div>
                <div class="specs-value">{{ $unit->body_type }}</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Engine Type</div>
                <div class="specs-value">{{ $unit->engine_type }}</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Displacement</div>
                <div class="specs-value">{{ $unit->displacement }} cc</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Starting System</div>
                <div class="specs-value">{{ $unit->starting_system }}</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Transmission</div>
                <div class="specs-value">{{ $unit->transmission }}</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Overall Dimensions: L x W x H</div>
                <div class="specs-value">{{ $unit->dimension }} (mm)</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Fuel Tank Capacity</div>
                <div class="specs-value">{{ $unit->fuel_tank_capacity }}</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Fuel System</div>
                <div class="specs-value">Carburetor</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Engine Oil</div>
                <div class="specs-value">{{ $unit->engine_oil }} Liter</div>
              </div>
              <div class="specs-content">
                <div class="specs-title">Net Weight</div>
                <div class="specs-value">{{ $unit->net_weight }} Liter</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="Content3">
        <div class="heading">
          <h1 class="Title">Explore More</h1>
        </div>
        <div class="home-product-row">
        @foreach ($product_list as $product)
          <div class="home-product-col">
            <img src="{{ asset('storage/'.$product->image_file) }}" alt="" />
            <h2 class="testi-name">{{ $product->model_name }}</h2>
            <div class="c3-buttons">
              <a href="../html/products.html">View Full Specs</a>
            </div>
          </div>
        @endforeach
        </div>
        <div class="c3-buttons">
          <a onclick="window.location.href = '/customer';">Send Application</a>
        </div>
      </section>
    </main>

    <!--JS Link-->
    <script type="text/javascript" src="../javascript/script.js"></script>