<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- Title & Basic Info -->
    <title>You Were There – Now Tell Us How It Was!</title>
    <meta name="description" content="Dear bride, groom, and guests, thank you for the joy! Let us know how we did by leaving your review of the wedding day we shared together.">

    <!-- Charset & Viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Keywords -->
    <meta name="keywords" content="wedding review, wedding feedback, bride review, groom review, guest wedding review">

    <!-- Author -->
    <meta name="author" content="Review Your Experience">

    <!-- Open Graph (Facebook / WhatsApp) -->
    <meta property="og:title" content="Review Your Experience – Share Your Experience">
    <meta property="og:description" content="Rate your guide, driver, hotel, attractions, and overall trip. Your feedback helps future travelers and improves services.">
    <meta property="og:image" content="http://reviewyourwedding.fwh.is/images/review.png"> <!-- Ganti sesuai gambar -->
    <meta property="og:url" content="http://reviewyourwedding.fwh.is">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Review Your Experience – Share Your Experience">
    <meta name="twitter:description" content="Rate your guide, driver, hotel, attractions, and overall trip. Your feedback helps future travelers and improves services.">
    <meta name="twitter:image" content="http://reviewyourwedding.fwh.is/images/review.png"> <!-- Ganti sesuai gambar -->

    <!-- Favicon -->
    <link rel="icon" href="http://reviewyourwedding.fwh.is/images/pavicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header">
            <h2 class="text-center" data-i18n="questionnaire">Questionnaire</h2>
        </div>
        <div class="text-end mb-3 text-center lang-container">
            <select id="languageSelector" class="form-select w-auto d-inline-block">
                <option value="en">🌐 English</option>
                <option value="zh-CN">🌐 简体中文</option>
                <option value="zh-TW">🌐 繁體中文</option>
            </select>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-5 ac-center">
                    <div class="hero-img">
                        <img src="/images/bali_travel_map_2.jpg">
                    </div>
                </div>
                <div class="col-md-7">
                    <ul class="info-container">
                        <li data-i18n="intro1">
                            Thank you for choosing us as your wedding planner in Bali, in order to 'BE BETTER', We must also continuously improve the service quality, we sincerely need your kind advise on the following questionnaire.
                        </li>
                        <li data-i18n="intro2">
                            Please fill out this review form to help us evaluate and improve the service of our team. Your feedback on our team and facilities is essential to ensure the best experience for all future guests.
                        </li>
                        <li data-i18n="intro3">
                            All required fields are marked with a *.
                        </li>
                        <li data-i18n="intro4">
                            Thank you for your time and support!
                        </li>
                    </ul>
                </div>
            </div>
            <form id="reviewForm" novalidate>
                <div class="row">
                    <!-- Before the Wedding -->
                    <div class="col-md-12">
                        <h5 class="mt-4 subheading" data-i18n="before_the_wedding">Before the Wedding</h5>
                        <div class="row p-lr-8">
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="communication_efficiency">Communication Efficiency</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="communication_efficiency" value="5" id="communication_efficiency_5"><label for="communication_efficiency_5">★</label>
                                    <input type="radio" name="communication_efficiency" value="4" id="communication_efficiency_4"><label for="communication_efficiency_4">★</label>
                                    <input type="radio" name="communication_efficiency" value="3" id="communication_efficiency_3"><label for="communication_efficiency_3">★</label>
                                    <input type="radio" name="communication_efficiency" value="2" id="communication_efficiency_2"><label for="communication_efficiency_2">★</label>
                                    <input type="radio" name="communication_efficiency" value="1" id="communication_efficiency_1"><label for="communication_efficiency_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="workflow_planning">Workflow Planning</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="workflow_planning" value="5" id="workflow_planning_5"><label for="workflow_planning_5">★</label>
                                    <input type="radio" name="workflow_planning" value="4" id="workflow_planning_4"><label for="workflow_planning_4">★</label>
                                    <input type="radio" name="workflow_planning" value="3" id="workflow_planning_3"><label for="workflow_planning_3">★</label>
                                    <input type="radio" name="workflow_planning" value="2" id="workflow_planning_2"><label for="workflow_planning_2">★</label>
                                    <input type="radio" name="workflow_planning" value="1" id="workflow_planning_1"><label for="workflow_planning_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="material_preparation">Materials Preparation</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="material_preparation" value="5" id="material_preparation_5"><label for="material_preparation_5">★</label>
                                    <input type="radio" name="material_preparation" value="4" id="material_preparation_4"><label for="material_preparation_4">★</label>
                                    <input type="radio" name="material_preparation" value="3" id="material_preparation_3"><label for="material_preparation_3">★</label>
                                    <input type="radio" name="material_preparation" value="2" id="material_preparation_2"><label for="material_preparation_2">★</label>
                                    <input type="radio" name="material_preparation" value="1" id="material_preparation_1"><label for="material_preparation_1">★</label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <!-- On the Wedding Day -->
                    <div class="col-md-12">
                        <h5 class="mt-4 subheading" data-i18n="on_the_wedding_day">On the Wedding Day</h5>
                        <div class="row p-lr-8">
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="service_attitude">Service Attitude</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="service_attitude" value="5" id="service_attitude_5"><label for="service_attitude_5">★</label>
                                    <input type="radio" name="service_attitude" value="4" id="service_attitude_4"><label for="service_attitude_4">★</label>
                                    <input type="radio" name="service_attitude" value="3" id="service_attitude_3"><label for="service_attitude_3">★</label>
                                    <input type="radio" name="service_attitude" value="2" id="service_attitude_2"><label for="service_attitude_2">★</label>
                                    <input type="radio" name="service_attitude" value="1" id="service_attitude_1"><label for="service_attitude_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="execution_of_workflow">Execution of Workflow</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="execution_of_workflow" value="5" id="execution_of_workflow_5"><label for="execution_of_workflow_5">★</label>
                                    <input type="radio" name="execution_of_workflow" value="4" id="execution_of_workflow_4"><label for="execution_of_workflow_4">★</label>
                                    <input type="radio" name="execution_of_workflow" value="3" id="execution_of_workflow_3"><label for="execution_of_workflow_3">★</label>
                                    <input type="radio" name="execution_of_workflow" value="2" id="execution_of_workflow_2"><label for="execution_of_workflow_2">★</label>
                                    <input type="radio" name="execution_of_workflow" value="1" id="execution_of_workflow_1"><label for="execution_of_workflow_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="time_management">Time Management</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="time_management" value="5" id="time_management_5"><label for="time_management_5">★</label>
                                    <input type="radio" name="time_management" value="4" id="time_management_4"><label for="time_management_4">★</label>
                                    <input type="radio" name="time_management" value="3" id="time_management_3"><label for="time_management_3">★</label>
                                    <input type="radio" name="time_management" value="2" id="time_management_2"><label for="time_management_2">★</label>
                                    <input type="radio" name="time_management" value="1" id="time_management_1"><label for="time_management_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="guest_care">Guest Care</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="guest_care" value="5" id="guest_care_5"><label for="guest_care_5">★</label>
                                    <input type="radio" name="guest_care" value="4" id="guest_care_4"><label for="guest_care_4">★</label>
                                    <input type="radio" name="guest_care" value="3" id="guest_care_3"><label for="guest_care_3">★</label>
                                    <input type="radio" name="guest_care" value="2" id="guest_care_2"><label for="guest_care_2">★</label>
                                    <input type="radio" name="guest_care" value="1" id="guest_care_1"><label for="guest_care_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="team_coordination">Team Coordination</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="team_coordination" value="5" id="team_coordination_5"><label for="team_coordination_5">★</label>
                                    <input type="radio" name="team_coordination" value="4" id="team_coordination_4"><label for="team_coordination_4">★</label>
                                    <input type="radio" name="team_coordination" value="3" id="team_coordination_3"><label for="team_coordination_3">★</label>
                                    <input type="radio" name="team_coordination" value="2" id="team_coordination_2"><label for="team_coordination_2">★</label>
                                    <input type="radio" name="team_coordination" value="1" id="team_coordination_1"><label for="team_coordination_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="third_party_coordination">Third Party Coordination</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="third_party_coordination" value="5" id="third_party_coordination_5"><label for="third_party_coordination_5">★</label>
                                    <input type="radio" name="third_party_coordination" value="4" id="third_party_coordination_4"><label for="third_party_coordination_4">★</label>
                                    <input type="radio" name="third_party_coordination" value="3" id="third_party_coordination_3"><label for="third_party_coordination_3">★</label>
                                    <input type="radio" name="third_party_coordination" value="2" id="third_party_coordination_2"><label for="third_party_coordination_2">★</label>
                                    <input type="radio" name="third_party_coordination" value="1" id="third_party_coordination_1"><label for="third_party_coordination_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="problem_solving_ability">Problem Solving Ability</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="problem_solving_ability" value="5" id="problem_solving_ability_5"><label for="problem_solving_ability_5">★</label>
                                    <input type="radio" name="problem_solving_ability" value="4" id="problem_solving_ability_4"><label for="problem_solving_ability_4">★</label>
                                    <input type="radio" name="problem_solving_ability" value="3" id="problem_solving_ability_3"><label for="problem_solving_ability_3">★</label>
                                    <input type="radio" name="problem_solving_ability" value="2" id="problem_solving_ability_2"><label for="problem_solving_ability_2">★</label>
                                    <input type="radio" name="problem_solving_ability" value="1" id="problem_solving_ability_1"><label for="problem_solving_ability_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-6" rating-group>
                                <label class="form-label"><span data-i18n="wrap_up_and_item_check">Wrap up & Item Check</span></label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-end">
                                    <input type="radio" name="wrap_up_and_item_check" value="5" id="wrap_up_and_item_check_5"><label for="wrap_up_and_item_check_5">★</label>
                                    <input type="radio" name="wrap_up_and_item_check" value="4" id="wrap_up_and_item_check_4"><label for="wrap_up_and_item_check_4">★</label>
                                    <input type="radio" name="wrap_up_and_item_check" value="3" id="wrap_up_and_item_check_3"><label for="wrap_up_and_item_check_3">★</label>
                                    <input type="radio" name="wrap_up_and_item_check" value="2" id="wrap_up_and_item_check_2"><label for="wrap_up_and_item_check_2">★</label>
                                    <input type="radio" name="wrap_up_and_item_check" value="1" id="wrap_up_and_item_check_1"><label for="wrap_up_and_item_check_1">★</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Travel Mood -->
                <h5 class="mt-4 subheading" data-i18n="your_detail">Your Details</h5>
                <div class="mb-3 p-lr-8">
                    <label class="form-label d-block"><span data-i18n="couple_mood">Mood</span><span class="label-required"> *</span></label>
                    <div class="d-flex-emo gap-3">
                        <div class="form-check text-center">
                            <input class="form-check-input d-none" type="radio" name="couple_mood" id="mood_happy" value="Very Satisfied" required>
                            <label class="form-check-label" for="mood_happy">
                                <div style="font-size: 2rem;font-size: 1.5rem !important; padding: 0 4px 0 0;">😊</div>
                                <div style="align-self: center;" data-i18n="very_satisfied_mood">Very Satisfied</div>
                            </label>
                        </div>
                        <div class="form-check text-center">
                            <input class="form-check-input d-none" type="radio" name="couple_mood" id="mood_satisfied" value="Satisfied">
                            <label class="form-check-label" for="mood_satisfied">
                                <div style="font-size: 2rem;font-size: 1.5rem !important; padding: 0 4px 0 0;">🙂</div>
                                <div style="align-self: center;" data-i18n="satisfied_mood">Satisfied</div>
                            </label>
                        </div>
                        <div class="form-check text-center">
                            <input class="form-check-input d-none" type="radio" name="couple_mood" id="mood_neutral" value="Neutral">
                            <label class="form-check-label" for="mood_neutral">
                                <div style="font-size: 2rem;font-size: 1.5rem !important; padding: 0 4px 0 0;">😐</div>
                                <div style="align-self: center;" data-i18n="normal_mood">Neutral</div>
                            </label>
                        </div>
                        <div class="form-check text-center">
                            <input class="form-check-input d-none" type="radio" name="couple_mood" id="mood_disappointed" value="Need Improvement">
                            <label class="form-check-label" for="mood_disappointed">
                                <div style="font-size: 2rem;font-size: 1.5rem !important; padding: 0 4px 0 0;">😞</div>
                                <div style="align-self: center;"data-i18n="need_improve_mood">Need Improvement</div>
                            </label>
                        </div>
                    </div>
                    <div class="invalid-feedback text-danger" id="error-couple_mood" style="display:none;" data-i18n="please_rate_this_field"></div>
                </div>

                <!-- Signature and Review -->
                <div class="mb-3 p-lr-8">
                    <label class="form-label"><span data-i18n="customer_name">Your Name</span><span class="label-required"> *</span></label>
                    <input type="text" class="form-control" name="customer_name" required />
                </div>

                <div class="mb-3 p-lr-8">
                    <label class="form-label"><span data-i18n="your_full_review">Your Full Review</span></label>
                    <textarea class="form-control" name="customer_review" rows="3"></textarea>
                </div>
                <div class="mb-3 p-lr-8" style="margin-left:24px;">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" />
                        <label class="form-check-label" for="agreeTerms">
                            <span data-i18n="i_agree_to_the">I agree to the</span>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" data-i18n="terms_and_conditions">Terms and Conditions</a> 
                            <span data-i18n="and">and</span> 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" data-i18n="privacy_policy">Privacy Policy</a>
                        </label>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="warningMessage" class="text-danger small" style="display:none;" data-i18n="check_terms">
                            Please agree to the Term and before submitting.
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="booking_code" value="<?php echo htmlspecialchars($bookingCode); ?>">
            </form>
            <div id="notificationBox" class="alert d-none mt-3" role="alert"></div>
        </div>
        <div class="card-footer" style="text-align: right;">
            <button id="submitBtn" type="submit" form="reviewForm" class="btn btn-primary" data-i18n="submit_review" disabled>Submit Review</button>
        </div>
    </div>
</div>
<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel" data-i18n="terms_and_conditions">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p data-i18n="tac_head">By submitting this review, you agree to the following terms and conditions:</p>
        <ol>
            <li data-i18n="tac_li1" >Your feedback is based on a real experience.</li>
            <li data-i18n="tac_li2" >We may use your input for service improvements.</li>
            <li data-i18n="tac_li3" >Personal data is handled confidentially.</li>
            <li data-i18n="tac_li4" >You agree that no compensation was promised for your review.</li>
            <li data-i18n="tac_li5" >We may edit or reject inappropriate content.</li>
        </ol>
        <p data-i18n="tac_li6">By continuing, you accept these terms.</p>
      </div>
    </div>
  </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="privacyModalLabel" data-i18n="privacy_policy">Privacy Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       
        <p data-i18n="pp_heading">We collect your review information to improve our services. Data collected includes:</p>
        <ul>
            <li data-i18n="pp_li1">Your name</li>
            <li data-i18n="pp_li2">Your feedback</li>
        </ul>
        <p data-i18n="pp_li3">All data is confidential, not shared with third parties, and may be retained unless deletion is requested.</p>
        <p data-i18n="pp_li4">By submitting, you agree to this policy.</p>
                
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
<script>
  window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
      // Paksa reload saat kembali dari cache history (misal klik back)
      window.location.reload();
    }
  });
</script>
</body>
</html>
