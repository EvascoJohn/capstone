@aware(['page'])
@props([
    'company_name',
    'heading_image',
    'hero_title',
    'register_button',
    'explore_button',
    'latest_products',
    'product_list',
    'requirements',
])

<section id="Content1">
    <div>
       <!-- Heading Image2 -->
      <img src="{{ asset('storage/'.$heading_image) }}" alt="section1-image-bg" />
      <div class="tagline-text">
        <h2>{{ $hero_title }}</h2>
        <div class="buttons">
          {{-- goes to the products section --}}
          <button class="explore-button" onclick="toggleProductSection()">
            {{ $explore_button }}
          </button>
          {{-- goes to the registration form --}}
          <button class="register-button" onclick="window.location.href = '/customer';">
            {{ $register_button }}
          </button>
        </div>
      </div>
    </div>
</section>


<section id="Content3" style="background-color: white;">
  <div class="heading">
    <h1 class="Title"> {{ $latest_products }} </h1>
  </div>
  <div class="home-product-row" style="background-color: white;">
    @foreach ($product_list as $product)
      <div class="home-product-col">
        <img src="{{ $product->image_file }}" alt="" />
        <h2 class="testi-name">{{ $product->model_name }}</h2>
        <div class="c3-buttons">
          <a href="/products/product-specs/{{$product->id}}">View Full Specs</a>
        </div>
      </div>
    @endforeach
  </div>
  <div class="c3-buttons">
    <a href="/products" >See More</a>
  </div>
</section>

<section id="Content4">
  <!--requirements-->
  <div class="heading">
    <h1 class="Title">REQUIREMENTS:</h1>
    <p class="content">
      Gear up with the essentials - the road to success begins here, armed
      with all the requirements you need.
    </p>
  </div>
  <div class="container">
    @foreach ($requirements as $requirement)
    <div class="specs-content">
      <div class="specs-title">{{ $requirement['requirement'] }}</div>
    </div>
  @endforeach
  </div>
  <div class="c4-buttons">
    {{-- <a href="../html/application.html">Application</a> --}}
  </div>
</section>