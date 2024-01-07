@aware(['page'])
@props([
    'heading_image',
    'hero_description',
    'brand_description_text',
    'read_more_button_text',
    'mission_text',
    'vision_text',
])

 <!--Hero section-->
<section id="Content1">
    <div>
        <img src="../images/about.jpg" alt="section1-image-bg" />
        <div class="tagline-text">
        <!--Hero Title-->
        <h2>About us</h2>

        <!--Hero description-->
        <p class="about-brand">
            {{ $hero_description }}
        </p>
        <div class="buttons">
        <!--Read more button-->
            <button class="explore-button" onclick="toggleProductSection()">
            {{ $read_more_button_text }}
            </button>
        </div>
        </div>
    </div>
</section>

    <section id="Content2">
        <div class="heading">
        <h1 class="Title">ABOUT US</h1>
        <p class="content">
            {{ $brand_description_text }}
        </p>
        <h1 class="Title">Our Mission</h1>
        <p class="content">
            {{ $mission_text }}
        </p>
        <h1 class="Title">Our Vision</h1>
        <p class="content">
            {{ $vision_text }}
        </p>
        </div>
    </section>