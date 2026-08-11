@section('title', __('messages.Service'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
<x-backend.page-hero :title="__('messages.Service')" />

            <div class="card-box pd-20 height-100-p mb-30">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <img src="images/balikami/banner-img.png" alt="">
                    </div>
                    <div class="col-md-8">
                        <h4 class="font-20 weight-500 mb-10 text-capitalize">
                            Welcome Admin <div class="weight-600 font-30 text-blue">{{ Auth::user()->name }}</div>
                        </h4>
                        <p class="font-18 max-width-600">Halaman ini adalah halaman yang hanya bisa diakses oleh user admin.
                            Admin dapat menambahkan, mengubah, menghapus, dan mengarsipkan data yang terdapat didalam sistem.
                            Gunakan hak akses anda untuk mengelola sistem ini dengan baik, agar tidak mengalami kendala ataupun
                            Error.</p>
                    </div>
                </div>
            </div>
            @include('layouts.counter')


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                <div class="card-box height-100-p overflow-hidden">
                    <div class="profile-tab height-100-p">
                        <div class="tab height-100-p">
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Tour
                                        Package</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tasks" role="tab">Hotel</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#setting" role="tab">Activity</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#transport" role="tab">Transport</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Tour Package Tab start -->
                                <div class="tab-pane fade show active" id="timeline" role="tabpanel">
                                    <div class="pd-20">
                                        <div class="card-box mb-30">
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5">
                                                    <h2 class="h4 pd-20" style="line-height: 2.5;"><img
                                                            src="images/icons/Tour.png" width="50" alt="">&nbsp; All
                                                        Tour Package</h2>
                                                </div>
                                                <div class="col-md-7 col-sm-7 text-right"
                                                    style="padding-right: 5%; padding-bottom: 2%">
                                                    <a href="tour-add" style="margin-top: 20px;" data-toggle="modal"
                                                        data-target="#tour-add"
                                                        class="bg-light-blue btn text-blue weight-500"><i
                                                            class="ion-plus-round"></i> Tambahkan Tour Package</a>
                                                </div>
                                            </div>
                                            <table class="data-table table nowrap" style="padding: 0 20px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%;">Name</th>
                                                        <th style="width: 15%;">Author</th>
                                                        <th style="width: 10%;">Created At</th>
                                                        <th style="width: 10%;">Last Update</th>

                                                        <th style="width: 15%;">Status</th>
                                                        <th style="width: 10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tours as $tour)
                                                        <tr>
                                                            <td>
                                                                <h5 class="font-16">{{ $tour['name'] }}</h5>
                                                                <i class="icon-copy fa fa-map-marker" style="color: red;"
                                                                    aria-hidden="true"></i> {{ $tour['destination'] }}
                                                            </td>
                                                            <td>
                                                                {{ $tour['duration'] }}
                                                            </td>
                                                            <td>
                                                                {{ $tour['type'] }}
                                                            </td>
                                                            <td>
                                                                {{ $tour['type'] }}
                                                            </td>
                                                            <td>
                                                                {{ '$ ' . number_format($tour['price'], 0, ',', '.') }}
                                                            </td>
                                                            <td>
                                                                <a href="tour-package-{{ $tour['id'] }}" data-toggle="tooltip"
                                                                    data-original-title="View Detail tour"><i
                                                                        class="dw dw-eye"></i></a> &nbsp; &nbsp;
                                                                <a href="tour-edit {{ $tour['id'] }}"data-toggle="tooltip"
                                                                    data-original-title="Edit data tour"><i
                                                                        class="icon-copy fa fa-edit"
                                                                        aria-hidden="true"></i></a>&nbsp; &nbsp;
                                                                <a href="tour-delete {{ $tour['id'] }}"data-toggle="tooltip"
                                                                    data-original-title="Delete data Tour"> <i
                                                                        class="icon-copy fa fa-trash-alt"
                                                                        aria-hidden="true"></i></a>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- End Tour Package on booking period ================================================================================ --}}

                                        <!-- add tour popup start -->
                                        <div class="modal fade customscroll" id="tour-add" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <div class="modal-title" id="exampleModalLongTitle">Tour Package</div>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" data-toggle="tooltip" data-placement="bottom"
                                                            title="" data-original-title="Close Modal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body pd-0">
                                                        <div class="task-list-form p-10">


                                                            <form action="/addtour" method="post"
                                                                enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="backend-form-field row">
                                                                    <h6 class="modal-title" id="exampleModalLongTitle">Image
                                                                    </h6>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Images</label>
                                                                    <div class="col-md-9">
                                                                        <input type="file"
                                                                            class="backend-form-control-file backend-form-control height-auto"
                                                                            name="images[]" multiple>
                                                                    </div>
                                                                </div>

                                                                <hr>
                                                                <div class="backend-form-field row">
                                                                    <h6 class="modal-title" id="exampleModalLongTitle">Detail
                                                                        Tour Package</h6>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Name</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="backend-form-control"
                                                                            name="name">
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Type</label>
                                                                    <div class="col-md-9">
                                                                        <select name="type"
                                                                            class="selectpicker"
                                                                            data-style="btn-outline-primary"
                                                                            title="Not Chosen">
                                                                            <option>Private</option>
                                                                            <option>Group</option>
                                                                            <option>Share</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Duration</label>
                                                                    <div class="col-md-9">
                                                                        <select name="duration"
                                                                            class="selectpicker"
                                                                            data-style="btn-outline-primary"
                                                                            title="Not Chosen">
                                                                            <option>1D</option>
                                                                            <option>2D/1N</option>
                                                                            <option>3D/2N</option>
                                                                            <option>4D/3N</option>
                                                                            <option>5D/4N</option>
                                                                            <option>6D/5N</option>
                                                                            <option>7D/6N</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Deskription</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" name="deskription" class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Destination</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" name="destination" class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Itinerary</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" itinerary class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Include</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" name="include" class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Exclude</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" name="exclude" class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Note</label>
                                                                    <div class="col-md-9">
                                                                        <textarea data-backend-richtext="true" name="note" class="textarea_editor backend-form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Price</label>
                                                                    <div class="col-md-9">
                                                                        <input name="price" type="number"
                                                                            class="backend-form-control">
                                                                    </div>
                                                                </div>

                                                                <div class="backend-form-field row">
                                                                    <label class="col-md-3">Quantity</label>
                                                                    <div class="col-md-9">
                                                                        <input name="quantity" type="number"
                                                                            class="backend-form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="backend-button backend-button-primary">Add Tour
                                                                        Package</button>
                                                                    <button type="button" class="backend-button backend-button-secondary"
                                                                        data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </form>


                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- end add tour popup start -->
                                    </div>
                                </div>
                                <!-- Tour Package Tab End -->









                                <!-- Tasks Tab start -->
                                <div class="tab-pane fade" id="tasks" role="tabpanel">
                                    <div class="pd-20 profile-task-wrap">
                                        <div class="container pd-0">
                                            <!-- Open Task start -->
                                            <div class="task-title row align-items-center">
                                                <div class="col-md-8 col-sm-12">
                                                    <h5>Open Tasks (4 Left)</h5>
                                                </div>
                                                <div class="col-md-4 col-sm-12 text-right">
                                                    <a href="task-add" data-toggle="modal" data-target="#task-add"
                                                        class="bg-light-blue btn text-blue weight-500"><i
                                                            class="ion-plus-round"></i> Add</a>
                                                </div>
                                            </div>
                                            <div class="profile-task-list pb-30">
                                                <ul>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-1">
                                                            <label class="custom-control-label" for="task-1"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id ea earum.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2019</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-2">
                                                            <label class="custom-control-label" for="task-2"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2019</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-3">
                                                            <label class="custom-control-label" for="task-3"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2019</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-4">
                                                            <label class="custom-control-label" for="task-4"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet. Id ea earum.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2019</span></div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- Open Task End -->











                                            <!-- Close Task start -->
                                            <div class="task-title row align-items-center">
                                                <div class="col-md-12 col-sm-12">
                                                    <h5>Closed Tasks</h5>
                                                </div>
                                            </div>
                                            <div class="profile-task-list close-tasks">
                                                <ul>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-close-1" checked="" disabled="">
                                                            <label class="custom-control-label" for="task-close-1"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id ea earum.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2018</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-close-2" checked="" disabled="">
                                                            <label class="custom-control-label" for="task-close-2"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2018</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-close-3" checked="" disabled="">
                                                            <label class="custom-control-label" for="task-close-3"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2018</span></div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="task-close-4" checked="" disabled="">
                                                            <label class="custom-control-label" for="task-close-4"></label>
                                                        </div>
                                                        <div class="task-type">Email</div>
                                                        Lorem ipsum dolor sit amet. Id ea earum.
                                                        <div class="task-assign">Assigned to Ferdinand M. <div
                                                                class="due-date">due date <span>22 February 2018</span></div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- Close Task start -->
                                            <!-- add task popup start -->
                                            <div class="modal fade customscroll" id="task-add" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Tasks Add</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close" data-toggle="tooltip"
                                                                data-placement="bottom" title=""
                                                                data-original-title="Close Modal">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body pd-0">
                                                            <div class="task-list-form">
                                                                <ul>
                                                                    <li>
                                                                        <form>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Task Type</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text"
                                                                                        class="backend-form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Task Message</label>
                                                                                <div class="col-md-8">
                                                                                    <textarea data-backend-richtext="true" class="textarea_editor backend-form-control"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Assigned to</label>
                                                                                <div class="col-md-8">
                                                                                    <select class="selectpicker"
                                                                                        data-style="btn-outline-primary"
                                                                                        title="Not Chosen" multiple=""
                                                                                        data-selected-text-format="count"
                                                                                        data-count-selected-text="{0} people selected">
                                                                                        <option>Ferdinand M.</option>
                                                                                        <option>Don H. Rabon</option>
                                                                                        <option>Ann P. Harris</option>
                                                                                        <option>Katie D. Verdin</option>
                                                                                        <option>Christopher S. Fulghum</option>
                                                                                        <option>Matthew C. Porter</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row mb-0">
                                                                                <label class="col-md-4">Due Date</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text"
                                                                                        class="backend-form-control date-picker">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:;" class="remove-task"
                                                                            data-toggle="tooltip" data-placement="bottom"
                                                                            title=""
                                                                            data-original-title="Remove Task"><i
                                                                                class="ion-minus-circled"></i></a>
                                                                        <form>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Task Type</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text"
                                                                                        class="backend-form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Task Message</label>
                                                                                <div class="col-md-8">
                                                                                    <textarea data-backend-richtext="true" class="textarea_editor backend-form-control"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row">
                                                                                <label class="col-md-4">Assigned to</label>
                                                                                <div class="col-md-8">
                                                                                    <select class="selectpicker"
                                                                                        data-style="btn-outline-primary"
                                                                                        title="Not Chosen" multiple=""
                                                                                        data-selected-text-format="count"
                                                                                        data-count-selected-text="{0} people selected">
                                                                                        <option>Ferdinand M.</option>
                                                                                        <option>Don H. Rabon</option>
                                                                                        <option>Ann P. Harris</option>
                                                                                        <option>Katie D. Verdin</option>
                                                                                        <option>Christopher S. Fulghum</option>
                                                                                        <option>Matthew C. Porter</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="backend-form-field row mb-0">
                                                                                <label class="col-md-4">Due Date</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text"
                                                                                        class="backend-form-control date-picker">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="add-more-task">
                                                                <a href="#" data-toggle="tooltip"
                                                                    data-placement="bottom" title=""
                                                                    data-original-title="Add Task"><i
                                                                        class="ion-plus-circled"></i> Add More Task</a>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="backend-button backend-button-primary">Add</button>
                                                            <button type="button" class="backend-button backend-button-secondary"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- add task popup End -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Tasks Tab End -->



                                <!-- Setting Tab start -->
                                <div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
                                    <div class="profile-setting">
                                        <form>
                                            <ul class="profile-edit-list row">
                                                <li class="weight-500 col-md-6">
                                                    <h4 class="text-blue h5 mb-20">Edit Your Personal Setting</h4>
                                                    <div class="backend-form-field">
                                                        <label>Full Name</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Title</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Email</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="email">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Date of birth</label>
                                                        <input class="backend-form-control backend-form-control-lg date-picker"
                                                            type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Gender</label>
                                                        <div class="d-flex">
                                                            <div class="custom-control custom-radio mb-5 mr-20">
                                                                <input type="radio" id="customRadio4" name="customRadio"
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label weight-400"
                                                                    for="customRadio4">Male</label>
                                                            </div>
                                                            <div class="custom-control custom-radio mb-5">
                                                                <input type="radio" id="customRadio5" name="customRadio"
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label weight-400"
                                                                    for="customRadio5">Female</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Country</label>
                                                        <select class="selectpicker  backend-form-control-lg"
                                                            data-style="btn-outline-secondary btn-lg" title="Not Chosen">
                                                            <option>United States</option>
                                                            <option>India</option>
                                                            <option>United Kingdom</option>
                                                        </select>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>State/Province/Region</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Postal Code</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Phone Number</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Address</label>
                                                        <textarea data-backend-richtext="true" class="textarea_editor backend-form-control"></textarea>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Visa Card Number</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Paypal ID</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="customCheck1-1">
                                                            <label class="custom-control-label weight-400"
                                                                for="customCheck1-1">I agree to receive notification
                                                                emails</label>
                                                        </div>
                                                    </div>
                                                    <div class="backend-form-field mb-0">
                                                        <input type="submit" class="backend-button backend-button-primary"
                                                            value="Update Information">
                                                    </div>
                                                </li>
                                                <li class="weight-500 col-md-6">
                                                    <h4 class="text-blue h5 mb-20">Edit Social Media links</h4>
                                                    <div class="backend-form-field">
                                                        <label>Facebook URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Twitter URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Linkedin URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Instagram URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Dribbble URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Dropbox URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Google-plus URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Pinterest URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Skype URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Vine URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field mb-0">
                                                        <input type="submit" class="backend-button backend-button-primary" value="Save & Update">
                                                    </div>
                                                </li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <!-- Setting Tab End -->
                                <!-- Transport Tab start -->
                                <div class="tab-pane fade height-100-p" id="transport" role="tabpanel">
                                    <div class="backend-form-field backend-form-search has-search" style="margin: 17px;	max-width: 47%; ">
                                        <input type="text" style="float:right;" class="backend-form-control"
                                            placeholder="Cari data Transport">
                                        <button type="button" class="backend-button backend-button-primary text-right">Cari Data</button>
                                    </div>
                                    <div class="profile-setting">
                                        <form>
                                            <ul class="profile-edit-list row">
                                                <li class="weight-500 col-md-6">
                                                    <h4 class="text-blue h5 mb-20">Edit data Transport</h4>
                                                    <div class="backend-form-field">
                                                        <label>Full Name</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Title</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Email</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="email">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Date of birth</label>
                                                        <input class="backend-form-control backend-form-control-lg date-picker"
                                                            type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Gender</label>
                                                        <div class="d-flex">
                                                            <div class="custom-control custom-radio mb-5 mr-20">
                                                                <input type="radio" id="customRadio4" name="customRadio"
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label weight-400"
                                                                    for="customRadio4">Male</label>
                                                            </div>
                                                            <div class="custom-control custom-radio mb-5">
                                                                <input type="radio" id="customRadio5" name="customRadio"
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label weight-400"
                                                                    for="customRadio5">Female</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Country</label>
                                                        <select class="selectpicker  backend-form-control-lg"
                                                            data-style="btn-outline-secondary btn-lg" title="Not Chosen">
                                                            <option>United States</option>
                                                            <option>India</option>
                                                            <option>United Kingdom</option>
                                                        </select>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>State/Province/Region</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Postal Code</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Phone Number</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Address</label>
                                                        <textarea data-backend-richtext="true" class="textarea_editor backend-form-control"></textarea>
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Visa Card Number</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Paypal ID</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <div class="custom-control custom-checkbox mb-5">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="customCheck1-1">
                                                            <label class="custom-control-label weight-400"
                                                                for="customCheck1-1">I agree to receive notification
                                                                emails</label>
                                                        </div>
                                                    </div>
                                                    <div class="backend-form-field mb-0">
                                                        <input type="submit" class="backend-button backend-button-primary"
                                                            value="Update Information">
                                                    </div>
                                                </li>
                                                <li class="weight-500 col-md-6">
                                                    <h4 class="text-blue h5 mb-20">Edit Social Media links</h4>
                                                    <div class="backend-form-field">
                                                        <label>Facebook URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Twitter URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Linkedin URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Instagram URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Dribbble URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Dropbox URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Google-plus URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Pinterest URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Skype URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field">
                                                        <label>Vine URL:</label>
                                                        <input class="backend-form-control backend-form-control-lg" type="text"
                                                            placeholder="Paste your link here">
                                                    </div>
                                                    <div class="backend-form-field mb-0">
                                                        <input type="submit" class="backend-button backend-button-primary" value="Save & Update">
                                                    </div>
                                                </li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <!-- Setting Tab End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-wrap pd-20 mb-20 card-box">
                Tourist data information system<br><a href="https://www.balikamitour.com" target="_blank">Bali Kami Tour &
                    Travel</a>
            </div>

        </div>
    @endcan
@endsection
