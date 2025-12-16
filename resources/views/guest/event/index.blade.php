@extends('guest.layouts.app')

@section('title', 'Events')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-3">Upcoming Events</h1>
            <p class="lead text-muted">Discover and join our exciting events</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Event Card 1 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-light text-dark">Upcoming</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Tech Conference 2025</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 25 January 2025
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Jakarta Convention Center
                    </p>
                    <p class="card-text">Join us for an exciting tech conference featuring the latest innovations in technology, networking opportunities, and keynote speakers from industry leaders.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-primary">Technology</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Card 2 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-light text-dark">Upcoming</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Workshop: Digital Marketing</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 15 February 2025
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Online Event
                    </p>
                    <p class="card-text">Learn the latest strategies and techniques in digital marketing. This workshop covers SEO, social media marketing, content strategy, and more.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-success">Workshop</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Card 3 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-light text-dark">Upcoming</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Networking Meetup</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 10 March 2025
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Co-working Space, Jakarta
                    </p>
                    <p class="card-text">Connect with professionals from various industries. A great opportunity to expand your network and share experiences with like-minded individuals.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-info">Networking</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Card 4 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-secondary">Past Event</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Annual Company Gathering</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 20 December 2024
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Grand Ballroom, Hotel
                    </p>
                    <p class="card-text">Our annual company gathering was a huge success! Thank you to everyone who attended and made it a memorable event.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-warning">Company Event</span>
                        <a href="#" class="btn btn-sm btn-outline-secondary">View Gallery</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Card 5 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-light text-dark">Upcoming</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Product Launch Event</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 5 April 2025
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Exhibition Hall
                    </p>
                    <p class="card-text">Be the first to see our latest product innovations. Join us for an exclusive launch event with live demonstrations and special offers.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-danger">Launch</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Register Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Card 6 -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-light text-dark">Upcoming</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fi fi-rr-calendar text-white" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Charity Fundraiser</h5>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-calendar me-2"></i> 18 May 2025
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fi fi-rr-marker me-2"></i> Community Center
                    </p>
                    <p class="card-text">Join us for a meaningful charity event to support local communities. Your participation makes a difference in people's lives.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-primary">Charity</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Donate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center p-5">
                    <h3 class="card-title mb-3">Stay Updated with Our Events</h3>
                    <p class="card-text mb-4">Subscribe to our newsletter to get notified about upcoming events and special announcements.</p>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your email address">
                                <button class="btn btn-light" type="button">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

