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

<!--Mission and Vission Section-->
<section id="Content3">
    <div class="heading">
        <!--Brand Title-->
        <h2 class="brandName"> {{ env('APP_NAME') }} </h2>
        <!--Brand Description-->
        <p class="brandDescription">
            {{ $brand_description_text }}
        </p>
    </div>
    <div class="mission-container">
        <div class="mission-image">
        <img src="../images/Banner.jpg" alt="mission-image" />
        </div>
        <div class="mission-text">
        <!--Mission-->
        <h2 class="mission">Mission</h2>
        <p class="mission-content">
            {{ $mission_text }}
        </p>
        </div>
    </div>
    <div class="vision-container">
        <div class="vision-text">
        <!--Vision-->
        <h2 class="vision">Vision</h2>
        <p class="vision-content">
            {{ $vision_text }}
        </p>
        </div>
        <div class="vision-image">
        <img src="../images/Banner.jpg" alt="vision-image" />
        </div>
    </div>
</section>