<!DOCTYPE html>
<html>
  <head>
    <!-- Meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- External CSS files -->
    <link rel="stylesheet" href="{{ asset('css/miranda/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/miranda/privacy-policy.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/miranda/media-queries.css') }}" />
    <!-- Google Fonts -->
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
    <title>Privacy Policy</title>
  </head>

  <body>

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

    <main>
      <section id="Content1">
          <img src="../images/privacy.jpg" alt="section1-image-bg" />
          <div class="tagline-text">
            <h2 class="animate">Privacy Policy</h2>
            <p class="contact-desc">
                Thank you for visiting our website. At {{env("APP_NAME")}}, we value your privacy like our own, guarding your data with utmost care.
            </p>
            <div class="buttons">
              <button class="inquire-button" onclick="toggleInquireSection()">
                READ MORE
              </button>
            </div>
          </div>
        </div>
      </section>

      <section id="Content2">
        <div class="heading">
          <h1 class="Title">Privacy Policy</h1>
          <p class="content">
            This Privacy Policy describes how we collect, use, and protect your personal information when you interact with our website. By using our website, you consent to the collection and use of your personal information as described in this Privacy Policy.
          </p>
          <h1 class="Title">Information We Collect</h1>
          <p class="content">
             We may collect various types of personal information from you, including but not limited to:
                <ul>
                    <li>Contact information (such as name, email address, phone number)</li>
                    <li>Demographic information (such as age, gender, location)</li>
                    <li>Payment information (such as credit card details)</li>
                    <li>Information you provide when contacting us or participating in surveys or promotions</li>
                </ul>
          </p>
          <h1 class="Title">How We Use Your Information</h1>
          <p class="content">
            We may use your personal information for the following purposes:
                <ul>
                    <li>To provide and maintain our services</li>
                    <li>To process and fulfill your orders</li>
                    <li>To communicate with you and respond to your inquiries</li>
                    <li>To improve our website and services</li>
                    <li>To personalize your experience and provide relevant content and offers</li>
                    <li>To analyze usage and trends and perform market research</li>
                </ul>
          </p>
          <h1 class="Title">Information Sharing and Disclosure</h1>
          <p class="content">
                 We may share your personal information with third parties in the following circumstances:
                <ul>
                    <li>With your consent</li>
                    <li>With service providers and business partners involved in delivering our services</li>
                    <li>With law enforcement agencies or government authorities when required by law</li>
                    <li>In connection with a merger, acquisition, or sale of our business</li>
                </ul>
          </p>
          <h1 class="Title">Data Security</h1>
          <p class="content">
                 We take appropriate measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. However, no data transmission over the internet or electronic storage is completely secure, and we cannot guarantee absolute security of your information.
          </p>
          <h1 class="Title">Your Rights</h1>
          <p class="content">
                 You have the right to access, update, and delete your personal information. You can also opt-out of receiving marketing communications from us. Please contact us using the information provided below to exercise your rights.
          </p>
          <h1 class="Title">Changes to this Privacy Policy</h1>
          <p class="content">
                  We may update this Privacy Policy from time to time. Any changes will be posted on this page, and the revised version will be effective as of the date it is posted.
          </p>
          <h1 class="Title">Contact Us</h1>
          <p class="content">
            If you have any questions or concerns regarding this Privacy Policy or our privacy practices, please contact us at:
                <br>
                email123@gmail.com
          </p>
        </div>
      </section>
    </main>



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
            <li><a href="../html/privacy-policy.html">Privacy Policy</a></li>
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

    <!--JS Link-->
    <script type="text/javascript" src="../javascript/script.js"></script>
    
  </body>
</html>
