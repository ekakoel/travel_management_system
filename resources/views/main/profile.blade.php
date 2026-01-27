@section('title', __('messages.Profile'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-p-b-18-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i>@lang('messages.User Profile')</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                    <li class="breadcrumb-item" aria-current="page"><a href="profile">@lang('messages.Profile')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ Auth::user()->email }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    @if (Auth::user()->status == "Block")
                        <div class="col-xl-12 m-b-18">
                            <div class="card-box p-b-18">
                            <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Your account has been disabled because it does not comply with the established terms.')</i>
                            </div>
                        </div>
                    @else
                        @if (!Auth::user()->email)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (!Auth::user()->phone)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (!Auth::user()->office)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (!Auth::user()->address)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (!Auth::user()->country)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (!Auth::user()->status)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Please complete your profile information first in order to use our services.')</i>
                                </div>
                            </div>
                        @elseif (Auth::user()->is_approved == 0)
                            <div class="col-xl-12 m-b-18">
                                <div class="card-box p-b-18">
                                <i style="color: red;"><i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i> @lang('messages.Your account is in the approval process, please wait for 2 x 24 hours for approval! Thank you.')</i>
                                </div>
                            </div>
                        @else
                        @endif
                    @endif
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 m-b-18">
                        <div class="card-box align-content-center">
                            <div class="profile-img-con">
                                @if (Auth::user()->profileimg == '')
                                    <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="{{ Auth::user()->name }}">
                                @else
                                    <img src="{{ asset('storage/user/profile' . '/' . Auth::user()->profileimg) }}" alt="{{ Auth::user()->name }}">
                                @endif
                            </div>
                            <div class="edit-btn-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Change Profile Picture')">
                                <a href="#" data-toggle="modal" data-target="#editProfilePicture" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                            </div>
                            {{-- MODAL --}}
                            <div class="modal fade"  id="editProfilePicture" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="position: absolute; left: 50%; transform: translate(-50%, 10px);" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title">@lang('messages.Change Profile Picture')</div>
                                            </div>
                                        
                                            <form id="update-profile-img" action="/fupdate-profileimg/{{ Auth::user()->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method("PUT")
                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <div class="dropzone text-center m-b-8">
                                                            <div class="profile-preview-div">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input type="file" name="profileimg" id="profileimg" style="width: 100%;" class="@error('profileimg') is-invalid @enderror" placeholder="Choose images" value="{{ Auth::user()->profileimg }}">
                                                            @error('messages.profileimg')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="update-profile-img" class="btn btn-info">@lang('messages.Change')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('messages.Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="text-center h5 mb-0">{{ Auth::user()->username }}</h5>
                            @if (Auth::user()->office != "")
                                <p style="margin: 0 0 15px;" class="text-center text-muted font-14">{{ "@". Auth::user()->office }}</p>
                            @endif
                            <hr class="form-hr m-b-8">
                            @if(Auth::user()->is_subscribed)
                                <div class="status-icon">
                                    <div class="icon subscribe">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                            <path d="M9 11l3 3L22 4"></path>
                                            <path d="M22 12A10 10 0 1 1 12 2a10 10 0 0 1 10 10z"></path>
                                        </svg>
                                        <span class="text">@lang('messages.Subscribed')</span>
                                    </div>
                                </div>
                                <p style="text-align: -webkit-center; color:grey;">@lang('messages.You will receive the latest promo information every week!')</p>
                                
                            @else
                                <div class="status-icon">
                                    <div class="icon unsubscribe">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FF5722" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                        </svg>
                                        <span class="text">@lang('messages.Unsubscribed')</span>
                                    </div>
                                </div>
                                <p style="text-align: -webkit-center; color:grey;">@lang('messages.You will no longer receive the latest promo information every week!')</p>
                                <div class="text-center">
                                    <a href="{{ route('subscribe', ['email' => Auth::user()->email]) }}">
                                        <button class="btn btn-primary">@lang('messages.Subscribe')</button>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-action">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @elseif (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 m-b-18">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">@lang('messages.Personal Information')</div>
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0">@lang('messages.User Name'):</h6>
                                <p><i>{{ Auth::user()->username }}</i></p>
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Name'):</h6>
                                <p><i>{{ Auth::user()->name }}</i></p>
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Email'):</h6>
                                <p>
                                    <i>{{ Auth::user()->email }}</i>
                                    @if (Auth::user()->email_verified_at == "")
                                        <i style="font-size: 0.7rem; color:rgb(255 0 0);">@lang('messages.Unverified')</i>
                                    @else
                                        <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Verified')" style="color: rgb(0, 167, 14); cursor:help" class="icon-copy fa fa-check-circle" aria-hidden="true"></i>
                                    @endif
                                </p>
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Phone'):</h6>
                                @if (Auth::user()->phone == "")
                                    <p><i style="color: red;">-</i></p>
                                @else
                                    <p><i>{{ Auth::user()->phone }}</i></p>
                                @endif
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Office'):</h6>
                                @if (Auth::user()->office == "")
                                    <p><i style="color: red;">-</p>
                                @else
                                    <p><i>{{ Auth::user()->office }}</i></p>
                                @endif
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Address'):</h6>
                                @if (Auth::user()->address == "")
                                    <p><i style="color: red;">-</i></p>
                                @else
                                    <p><i>{{ Auth::user()->address }}</i></p>
                                @endif
                            </div>
                            <div class="page-list">
                                <h6 class="p-b-0 p-t-8"> @lang('messages.Country'):</h6>
                                @if (Auth::user()->country == "")
                                    <p><i style="color: red;">-</i></p>
                                @else
                                    <p><i>{{ Auth::user()->country }}</i></p>
                                @endif
                            </div>
                            @if (Auth::user()->status != "Block")
                                <div class="card-box-footer">
                                    {{-- @if (Auth::user()->is_approved != 1) --}}
                                        <a href="#" data-toggle="modal" data-target="#modal{{ Auth::user()->id }}">
                                            <button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Edit Profile')</button>
                                        </a>
                                    {{-- @endif --}}
                                    <a href="#" data-toggle="modal" data-target="#update-password" class="edit-avatar"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Change Password')</button></a>
                                </div>
                                <div class="modal fade" id="modal{{ Auth::user()->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    @lang('messages.Update Profile')
                                                </div>
                                                <form id="update-profile" action="/fupdate-profile/{{ Auth::user()->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    <div class="form-group">
                                                        <label>@lang('messages.Username')</label>
                                                        <input type="text" class="form-control" placeholder="{{ Auth::user()->username }}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('messages.Email')</label>
                                                        <input class="form-control" type="email" value="{{ Auth::user()->email }}" disabled>
                                                        <i class="alert-text"> &nbsp; &nbsp; @lang('messages.Username and E-mail can not be change!')</i>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('messages.Name')</label>
                                                        <input name="name" class="form-control @error('name') is-invalid @enderror" type="text"value="{{ Auth::user()->name }}" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('messages.Office')</label>
                                                        <input name="office" class="form-control @error('office') is-invalid @enderror" type="text" value="{{ Auth::user()->office }}" required>
                                                        @error('office')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>@lang('messages.Phone')</label>
                                                        <input name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="ex: 62888888888" type="number" value="{{ Auth::user()->phone }}" required>
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('messages.Address')</label>
                                                        <input name="address" class="form-control @error('address') is-invalid @enderror" value="{{ Auth::user()->address }}" required>
                                                        @error('address')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>@lang('messages.Country')<span> *</span></label>
                                                        <select name="country" id="country" class="custom-select @error('country') is-invalid @enderror" required>
                                                            @if (Auth::user()->country)
                                                                <option value="{{ Auth::user()->country }}">{{ Auth::user()->country }}</option>
                                                            @else
                                                                <option value="">Country...</option>
                                                            @endif
                                                            <option value="Afganistan">Afghanistan</option>
                                                            <option value="Albania">Albania</option>
                                                            <option value="Algeria">Algeria</option>
                                                            <option value="American Samoa">American Samoa</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Anguilla">Anguilla</option>
                                                            <option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Aruba">Aruba</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Austria">Austria</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Barbados">Barbados</option>
                                                            <option value="Belarus">Belarus</option>
                                                            <option value="Belgium">Belgium</option>
                                                            <option value="Belize">Belize</option>
                                                            <option value="Benin">Benin</option>
                                                            <option value="Bermuda">Bermuda</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Bolivia">Bolivia</option>
                                                            <option value="Bonaire">Bonaire</option>
                                                            <option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
                                                            <option value="Botswana">Botswana</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgaria</option>
                                                            <option value="Burkina Faso">Burkina Faso</option>
                                                            <option value="Burundi">Burundi</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="Cameroon">Cameroon</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Canary Islands">Canary Islands</option>
                                                            <option value="Cape Verde">Cape Verde</option>
                                                            <option value="Cayman Islands">Cayman Islands</option>
                                                            <option value="Central African Republic">Central African Republic</option>
                                                            <option value="Chad">Chad</option>
                                                            <option value="Channel Islands">Channel Islands</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Christmas Island">Christmas Island</option>
                                                            <option value="Cocos Island">Cocos Island</option>
                                                            <option value="Colombia">Colombia</option>
                                                            <option value="Comoros">Comoros</option>
                                                            <option value="Congo">Congo</option>
                                                            <option value="Cook Islands">Cook Islands</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Cote DIvoire">Cote D'Ivoire</option>
                                                            <option value="Croatia">Croatia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Curaco">Curacao</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Czech Republic">Czech Republic</option>
                                                            <option value="Denmark">Denmark</option>
                                                            <option value="Djibouti">Djibouti</option>
                                                            <option value="Dominica">Dominica</option>
                                                            <option value="Dominican Republic">Dominican Republic</option>
                                                            <option value="East Timor">East Timor</option>
                                                            <option value="Ecuador">Ecuador</option>
                                                            <option value="Egypt">Egypt</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                            <option value="Eritrea">Eritrea</option>
                                                            <option value="Estonia">Estonia</option>
                                                            <option value="Ethiopia">Ethiopia</option>
                                                            <option value="Falkland Islands">Falkland Islands</option>
                                                            <option value="Faroe Islands">Faroe Islands</option>
                                                            <option value="Fiji">Fiji</option>
                                                            <option value="Finland">Finland</option>
                                                            <option value="France">France</option>
                                                            <option value="French Guiana">French Guiana</option>
                                                            <option value="French Polynesia">French Polynesia</option>
                                                            <option value="French Southern Ter">French Southern Ter</option>
                                                            <option value="Gabon">Gabon</option>
                                                            <option value="Gambia">Gambia</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="Ghana">Ghana</option>
                                                            <option value="Gibraltar">Gibraltar</option>
                                                            <option value="Great Britain">Great Britain</option>
                                                            <option value="Greece">Greece</option>
                                                            <option value="Greenland">Greenland</option>
                                                            <option value="Grenada">Grenada</option>
                                                            <option value="Guadeloupe">Guadeloupe</option>
                                                            <option value="Guam">Guam</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guinea">Guinea</option>
                                                            <option value="Guyana">Guyana</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Hawaii">Hawaii</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hong Kong">Hong Kong</option>
                                                            <option value="Hungary">Hungary</option>
                                                            <option value="Iceland">Iceland</option>
                                                            <option value="India">India</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Iran">Iran</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Ireland">Ireland</option>
                                                            <option value="Isle of Man">Isle of Man</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italy">Italy</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kenya">Kenya</option>
                                                            <option value="Kiribati">Kiribati</option>
                                                            <option value="Korea North">Korea North</option>
                                                            <option value="Korea Sout">Korea South</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Latvia">Latvia</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Lesotho">Lesotho</option>
                                                            <option value="Liberia">Liberia</option>
                                                            <option value="Libya">Libya</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lithuania">Lithuania</option>
                                                            <option value="Luxembourg">Luxembourg</option>
                                                            <option value="Macau">Macau</option>
                                                            <option value="Macedonia">Macedonia</option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mali">Mali</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marshall Islands">Marshall Islands</option>
                                                            <option value="Martinique">Martinique</option>
                                                            <option value="Mauritania">Mauritania</option>
                                                            <option value="Mauritius">Mauritius</option>
                                                            <option value="Mayotte">Mayotte</option>
                                                            <option value="Mexico">Mexico</option>
                                                            <option value="Midway Islands">Midway Islands</option>
                                                            <option value="Moldova">Moldova</option>
                                                            <option value="Monaco">Monaco</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Montserrat">Montserrat</option>
                                                            <option value="Morocco">Morocco</option>
                                                            <option value="Mozambique">Mozambique</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Nambia">Nambia</option>
                                                            <option value="Nauru">Nauru</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Netherland Antilles">Netherland Antilles</option>
                                                            <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                                            <option value="Nevis">Nevis</option>
                                                            <option value="New Caledonia">New Caledonia</option>
                                                            <option value="New Zealand">New Zealand</option>
                                                            <option value="Nicaragua">Nicaragua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigeria</option>
                                                            <option value="Niue">Niue</option>
                                                            <option value="Norfolk Island">Norfolk Island</option>
                                                            <option value="Norway">Norway</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palau Island">Palau Island</option>
                                                            <option value="Palestine">Palestine</option>
                                                            <option value="Panama">Panama</option>
                                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                                            <option value="Paraguay">Paraguay</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Phillipines">Philippines</option>
                                                            <option value="Pitcairn Island">Pitcairn Island</option>
                                                            <option value="Poland">Poland</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Puerto Rico">Puerto Rico</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Republic of Montenegro">Republic of Montenegro</option>
                                                            <option value="Republic of Serbia">Republic of Serbia</option>
                                                            <option value="Reunion">Reunion</option>
                                                            <option value="Romania">Romania</option>
                                                            <option value="Russia">Russia</option>
                                                            <option value="Rwanda">Rwanda</option>
                                                            <option value="St Barthelemy">St Barthelemy</option>
                                                            <option value="St Eustatius">St Eustatius</option>
                                                            <option value="St Helena">St Helena</option>
                                                            <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                                            <option value="St Lucia">St Lucia</option>
                                                            <option value="St Maarten">St Maarten</option>
                                                            <option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
                                                            <option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
                                                            <option value="Saipan">Saipan</option>
                                                            <option value="Samoa">Samoa</option>
                                                            <option value="Samoa American">Samoa American</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Sao Tome &amp; Principe">Sao Tome &amp; Principe</option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Serbia">Serbia</option>
                                                            <option value="Seychelles">Seychelles</option>
                                                            <option value="Sierra Leone">Sierra Leone</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="Slovakia">Slovakia</option>
                                                            <option value="Slovenia">Slovenia</option>
                                                            <option value="Solomon Islands">Solomon Islands</option>
                                                            <option value="Somalia">Somalia</option>
                                                            <option value="South Africa">South Africa</option>
                                                            <option value="Spain">Spain</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Sudan">Sudan</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Swaziland">Swaziland</option>
                                                            <option value="Sweden">Sweden</option>
                                                            <option value="Switzerland">Switzerland</option>
                                                            <option value="Syria">Syria</option>
                                                            <option value="Tahiti">Tahiti</option>
                                                            <option value="Taiwan">Taiwan</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Tanzania">Tanzania</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tokelau">Tokelau</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
                                                            <option value="Tunisia">Tunisia</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Erimates">United Arab Emirates</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="United States of America">United States of America</option>
                                                            <option value="Uraguay">Uruguay</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vanuatu">Vanuatu</option>
                                                            <option value="Vatican City State">Vatican City State</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietnam">Vietnam</option>
                                                            <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                                            <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                                            <option value="Wake Island">Wake Island</option>
                                                            <option value="Wallis &amp; Futana Is">Wallis &amp; Futana Is</option>
                                                            <option value="Yemen">Yemen</option>
                                                            <option value="Zaire">Zaire</option>
                                                            <option value="Zambia">Zambia</option>
                                                            <option value="Zimbabwe">Zimbabwe</option>
                                                        </select>
                                                        {{-- <input name="country" class="form-control @error('country') is-invalid @enderror" type="text" value="{{ Auth::user()->country }}" required> --}}
                                                        @error('country')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="update-profile" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Update')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i>  @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- UPDATE PASSWORD MODAL  --}}
                                <div class="modal fade" id="update-password" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title">@lang('messages.Change Password')</div>
                                                </div>
                                                <form id="update-password" action="{{ route('update-password') }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('put')
                                                    
                                                        <div class="form-group">
                                                            <label for="oldPasswordInput" clas="form-label">@lang('messages.Old Password')</label>
                                                            <input id="oldPasswordInput" name="old_password" class="form-control @error('old_password') is-invalid @enderror" type="password" value="{{ old('old_password') }}" required>
                                                            @error('old_password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="newPasswordInput" clas="form-label">@lang('messages.New Password')</label>
                                                            <input id="newPasswordInput" name="new_password" class="form-control @error('new_password') is-invalid @enderror" type="password" value="{{ old('new_password') }}" required>
                                                            @error('new_password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="confirmNewPasswordInput" clas="form-label">@lang('messages.Confirm New Password')</label>
                                                            <input id="confirmNewPasswordInput" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" type="password" value="{{ old('new_password_confirmation') }}" required>
                                                            @error('new_password_confirmation')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="update-password" class="btn btn-primary" value="@lang('messages.Change')"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Change')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> 
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
