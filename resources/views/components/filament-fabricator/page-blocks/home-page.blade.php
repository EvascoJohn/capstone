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
          <button class="register-button" onclick="window.location.href = '/customer/register';">
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
        <img src="{{ asset('storage/'.$product->image_file) }}" alt="" />
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

<section id="Content4" style="background-color: white">
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
    <a href="/customer/login">Send Application Now</a>
  </div>
</section>

<section id="Content6" style="background-color: white">
  <!--ratings-->
  <h1>What Our Customer Says</h1>
  <p>
    Echoes of Satisfaction: Unveiling the Stories and Smiles Shared by Our
    Valued Customers
  </p>

  <div class="testimonial-row">
    <div class="testimonial-col">
      <p>
        <span>"</span>I am extremely pleased with my recent purchase from
        this website. The variety of motorcycles available made it easy
        for me to find the perfect match. The ordering process was
        straightforward. The bike arrived in pristine condition, and I
        can't wait to hit the road.<span>"</span>
      </p>
      <h2 class="testi-name">John Doe</h2>
    </div>
    <div class="testimonial-col">
      <p>
        <span>"</span>I had a seamless experience buying a motorcycle from
        this website. The website design is user-friendly, and I
        appreciate the detailed specifications provided for each bike. The
        transaction process was quick and secure.<span>"</span>
      </p>
      <h2 class="testi-name">Michael Johnson</h2>
    </div>
    <div class="testimonial-col">
      <p>
        <span>"</span>I can't express how impressed I am with the level of
        service I received from this online motorcycle store. The staff
        was responsive and went above and beyond to answer my queries. The
        bike I ordered was exactly as described, and the entire process
        from browsing to delivery was a breeze.<span>"</span>
      </p>
      <h2 class="testi-name">Jessica Miller</h2>
    </div>
  </div>
  <div class="back-to-top">
    <button class="btt-button" onclick="toggleTopSection()">
      Back to top <i class="ri-arrow-up-s-line"></i>
    </button>
  </div>
</section>