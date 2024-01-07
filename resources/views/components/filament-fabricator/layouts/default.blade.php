@props([
    'page',
    'company_name',
    'heading_image',
    'hero_title',
    'register_button',
    'explore_button',
    'latest_products',
    'product_list',
    'requirements',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/miranda/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/miranda/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/miranda/media-queries.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('css/own/product-page-specs.css') }}" /> --}}
    <title>Motorstar - Probike Motorcycle Center</title>
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
    <x-filament-fabricator::layouts.base :title="$page->title">
    {{-- Header Here --}}
    <header>
      <!-- Brand logo Text -->
      <a href="#" class="logo">{{ env('APP_NAME') }}</a>
      <!-- Navigation bar -->
      <ul class="navbar">
          {{-- redirects to the home --}}
          <li>
              <a href="/home" class="{{ request()->is('home') ? 'active': 'hidden' }}">Home</a>
          </li>
          {{-- redirects to the products --}}
          <li>
              <a href="/products" class="{{ request()->is('products') ? 'active': 'hidden' }}">Products</a>
          </li>
          <li>
              @auth<a href="/application" class="{{ request()->is('application') ? 'active': 'hidden' }}">Application</a>@endauth
          </li>
          {{-- redirects to the about --}}
          <li>
              <a href="/about-us" class="{{ request()->is('about-us') ? 'active': 'hidden' }}">About Us</a>
          </li>
          {{-- redirects to the contact --}}
          <li>
              <a href="/contact-us" class="{{ request()->is('contact-us') ? 'active': 'hidden' }}">Contact Us</a>
          </li>
      </ul>
      <!-- User registration and menu icon -->
      <div class="main">
          <a href="/customer" class="user" id="loginLink">Log In 
              <i class="ri-account-circle-fill"></i>
          </a>
          <div class="ri-menu-line" id="menu-icon"></div>
      </div>
    </header>
    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
     {{-- Footer Here --}}
         <!--Footer Design-->
    <footer>
      <div class="content">
        <div class="row1 box">
          <div class="upper">
            <div class="topic">Motorstar</div>
            <p>
              Motorstar Philippines is a brand with Filipino-assembled
              motorcycles with most of its parts coming from China. Established
              in February 2002, its motorbikes and scooters are exclusively
              assembled and distributed by Eastworld Motor Industries
              Corporation (EMIC).
            </p>
          </div>
        </div>

        <div class="row2 box">
          <li> <a href="/home" class="{{ request()->is('home') ? 'active': 'hidden' }}">Home</a> </li>
          <li> <a href="/products" class="{{ request()->is('products') ? 'active': 'hidden' }}">Products</a> </li>
          <br />
          <li><a href="/privacy-policy">Privacy Policy</a></li>
          <li>
            <a href="/terms-and-conditions">Terms and Condition</a>
          </li>
          <li><a href="/contact-us" class="{{ request()->is('contact-us') ? 'active': 'hidden' }}>FAQs</a></li>
        </div>

        <div class="row3 box">
          <div class="topic">Contact us</div>
          <div class="email">
            <a
              href="https://mail.google.com/mail/u/0/#inbox?compose=CllgCJqWgTkZZRhQvVlgWGtxZRzLQWJgBMPDJbFRdXNNVMcMVrNtrGLNksvXJDDmwLTzWcVWwSV"
              target="_blank"
              ><i class="ri-mail-fill"></i>probikesmotorstar@email.com</a
            >
          </div>
          <div class="address">
            <a
              href="https://www.google.com/maps/place/Motorstar/@14.0747366,120.6313664,21z/data=!4m14!1m7!3m6!1s0x33bd96b1df99308b:0x4bc469e948fcecd9!2sMotorstar!8m2!3d14.074798!4d120.6314965!16s%2Fg%2F1hhmq6n_4!3m5!1s0x33bd96b1df99308b:0x4bc469e948fcecd9!8m2!3d14.074798!4d120.6314965!16s%2Fg%2F1hhmq6n_4?entry=ttu"
              target="_blank"
              ><i class="ri-navigation-fill"></i>Nasugbu, Batangas</a
            >
          </div>
        </div>

        <div class="row4 box">
          <div class="topic">Sign up to our Newsletter</div>
          <form action="#">
            <input type="text" placeholder="Enter email address" required="" />
            <input type="submit" name="" value="Send" />
            <div class="media-icons">
              <a href="http://facebook.com" target="_blank"
                ><i class="ri-facebook-fill ri-1x"></i
              ></a>
              <a href="http://instagram.com" target="_blank"
                ><i class="ri-instagram-fill ri-1x"></i
              ></a>
              <a href="http://twitter.com" target="_blank"
                ><i class="ri-twitter-fill ri-1x"></i
              ></a>
              <a href="http://messenger.com" target="_blank"
                ><i class="ri-messenger-fill ri-1x"></i
              ></a>
            </div>
          </form>
        </div>
      </div>
      <div class="bottom">
        <p>Copyright © 2023 <span>Motorstar</span> Made by Group5</p>
      </div>
    </footer>
    </x-filament-fabricator::layouts.base>
    @filamentScripts
    <script type="text/javascript" src="{{ asset('js/own/script.js') }}"></script>
</body>
</html>
