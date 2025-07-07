<?php
// Database connection
include('includes/db.php');
// Header include
include('includes/header.php');
?>

<!-- hero area -->
<section>
    <div class="hero-slider-2 position-relative">
        <!-- hero slider items -->
        <div class="hero-slider-item py-160" style="background-image: url(images/banner/banner-1.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="hero-content">
                            <h4 class="text-uppercase mb-1">Greater utilisation of</h4>
                            <h1 class="font-weight-bold mb-3">Political Scientists</h1>
                            <p class="text-dark mb-50">As experts in the field of Governance</p>
                            <a href="about.html" class="btn btn-outline text-uppercase">more details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-slider-item py-160" style="background-image: url(images/banner/banner-2.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="hero-content">
                            <h4 class="text-uppercase mb-1">Promoting Interchange</h4>
                            <h1 class="font-weight-bold mb-3">Among all Institutions</h1>
                            <p class="text-dark mb-50">and Individuals who analyse, study, teach, and contribute actively<br>to the civic and political life in Malawi.</p>
                            <a href="about-2.html" class="btn btn-outline text-uppercase">more details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-slider-item py-160" style="background-image: url(images/banner/banner-3.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="hero-content">
                            <h4 class="text-uppercase mb-1">Promoting knowledge and understanding of</h4>
                            <h1 class="font-weight-bold mb-3">Local and International</h1>
                            <p class="text-dark mb-50">Political affairs through teaching, workshops, discussion, conferences,<br> and publications or such other means.</p>
                            <a href="about.html" class="btn btn-outline text-uppercase">more details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-slider-item py-160" style="background-image: url(images/banner/banner-4.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="hero-content">
                            <h4 class="text-uppercase mb-1">We promote the study</h4>
                            <h1 class="font-weight-bold mb-3">Research & publications</h1>
                            <p class="text-dark mb-50">Related to but not limited to Malawi politics.</p>
                            <a href="about-2.html" class="btn btn-outline text-uppercase">more details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- cta -->
<section class="bg-primary py-4 text-center text-lg-left">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 align-self-center">
                <h3 class="text-white">We are Affiliated to African APS</h3>
            </div>
            <div class="col-lg-3 text-lg-right">
                <a href="contact.html" class="btn btn-light btn-sm">BECOME OUR MEMBER</a>
            </div>
        </div>
    </div>
</section>

<!-- service -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 align-self-center">
                <h5 class="section-title-sm">Who we are</h5>
                <h2 class="section-title section-title-border-half">The Malawi Political Science Association (PSA)</h2>
                <p class="mb-4">We are a registered entity with a diverse membership of academics and professionals in various fields.</p>
                <p class="mb-4">According to Article 4(1) of PSA Constitution, the Association is nonpartisan and does not take positions nor commit its members to particular political positions not immediately concerned with its purpose.</p>
                <a href="about.html" class="btn btn-primary mb-md-50">Explore More</a>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-4 card px-2 py-5 text-center">
                            <i class="h2 text-primary d-inline-block mb-20 ti-medall"></i>
                            <h4>Premium Members</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-4 card px-2 py-5 text-center">
                            <i class="h2 text-primary d-inline-block mb-20 ti-medall-alt"></i>
                            <h4>Associate Members</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-4 card px-2 py-5 text-center">
                            <i class="h2 text-primary d-inline-block mb-20 ti-star"></i>
                            <h4>Honorary Members</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-4 card px-2 py-5 text-center">
                            <i class="h2 text-primary d-inline-block mb-20 ti-user"></i>
                            <h4>Institutional Members</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- dynamic news & events section -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Latest News -->
            <div class="col-lg-6">
                <div class="about-content">
                    <h3 class="section-title section-title-border-half">Latest News</h3>
                    <div class="about-item">
                        <ul class="pl-0 d-inline-block float-sm-left mr-sm-5">
                            <?php
                            $news = mysqli_query($conn, "SELECT * FROM news ORDER BY created_at DESC LIMIT 6");
                            while($n = mysqli_fetch_assoc($news)) {
                                echo '<li class="font-secondary text-color mb-10">'
                                    . '<i class="text-primary mr-2 ti-arrow-circle-right"></i>'
                                    . '<a href="news-details.php?id=' . $n['id'] . '">' . $n['title'] . '</a>'
                                    . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <a href="news.php" class="btn btn-primary mb-md-50 mt-4">More</a>
                </div>
            </div>

            <!-- Events -->
            <div class="col-lg-6">
                <article class="bg-white rounded mb-40">
                    <h3 class="section-title section-title-border-half">Announcements & Events</h3>
                    <div class="post-slider">
                        <?php
                        $events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC LIMIT 3");
                        while($event = mysqli_fetch_assoc($events)) {
                            echo '<div>';
                            echo '<div class="d-flex align-items-center border-bottom">';
                            echo '<div class="text-center border-right p-4">';
                            echo '<h3 class="text-primary mb-0">' . date('d', strtotime($event['event_date'])) .
                                '<span class="d-block paragraph">' . date('M Y', strtotime($event['event_date'])) . '</span></h3>';
                            echo '</div>';
                            echo '<div class="px-4">';
                            echo '<a class="h4 d-block mb-10" href="event-details.php?id=' . $event['id'] . '">' . $event['title'] . '</a>';
                            echo '<ul class="post-meta list-inline">';
                            echo '<li class="list-inline-item paragraph mr-5">By <a class="paragraph">' . $event['author'] . '</a></li>';
                            echo '<li class="list-inline-item paragraph">' . $event['tags'] . '</li>';
                            echo '</ul></div></div>';
                            echo '<div class="p-4"><p>' . $event['description'] . '</p></div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- partner logos -->
<section class="bg-primary py-4">
    <div class="container">
        <div class="client-logo-slider align-self-center">
            <!-- logos -->
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>
