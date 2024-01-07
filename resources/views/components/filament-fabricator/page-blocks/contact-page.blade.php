@aware(['page'])
@props([
    "hero_description",
    "heading_image",
    "inquire_button",
    "check_location_button",
    "frequently_asked_questions",
    "head_office_address",
    "contact_number",
    "email_address",
    "",
])

<!-- Hero Section -->
<section id="Content1">
    <img src="../images/Contact.jpg" alt="section1-image-bg" />
    <div class="tagline-text">
      <h2 class="animate">Contact us</h2>
      <p class="contact-desc">
        {{ $hero_description }}
      </p>
      <div class="buttons">
        <button class="inquire-button" onclick="toggleInquireSection()">
          {{ $inquire_button }}
        </button>
        <button
          class="check-location-button"
          onclick="toggleMapSection()"
        >
        {{ $check_location_button }}
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Contact Section -->
<section id="Content2">
  <div class="heading">
    <h2 class="brandName">GET IN TOUCH</h2>
    <p class="brandDescription">
      Please contact us using the information below.
    </p>
  </div>
  <div class="row">
    <div class="contact-col">
      <h2>Contact Information</h2>
      <div>
        <i class="ri-building-fill"></i>
        <span>
          <h5>Head Office</h5>
          <p>Nasugbu, Batangas</p>
        </span>
      </div>
      <div>
        <i class="ri-phone-fill"></i>
        <span>
          <h5>Our Contacts</h5>
          <p>+639123456789</p>
        </span>
      </div>
      <div>
        <i class="ri-mail-fill"></i>
        <span>
          <h5>Email</h5>
          <p>probikesmotorstar@gmail.com</p>
        </span>
      </div>
      <div>
        <i class="ri-time-fill"></i>
        <span>
          <h5>Office Hours</h5>
          <p>Monday - Sunday</p>
          <p>8:00 am - 5:00pm</p>
        </span>
      </div>
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
    </div>
  </div>
</section>



<section id="Content3">
  <div class="container">
    <h2>Look Us on the Map</h2>
    <div class="map-container">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1827.4434493536141!2d120.73022125830036!3d13.945656663193368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bda2df3c56e6cf%3A0xde994034e08b1924!2sSTI%20College%20Balayan!5e0!3m2!1sen!2sph!4v1691129700540!5m2!1sen!2sph" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>


<!-- Frequently Asked Question Section -->
<section id="Content4">
    <h2>Frequently Ask Questions</h2>
@foreach ($frequently_asked_questions as $questions)
<ul id="accordion">
    <li>
        <label for="{{ $loop->index + 1 }}">{{ $questions["question"] }} <span>&#x3e;</span></label>
        <input type="checkbox" name="accordion" id="{{ $loop->index + 1 }}">
        <div class="content">
            <p>
                {{ $questions["answer"] }}
            </p>
        </div>
    </li>
</ul>
@endforeach
</section>