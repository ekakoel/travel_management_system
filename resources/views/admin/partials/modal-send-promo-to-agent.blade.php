<div class="modal fade" id="send-email-{{ $promo->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card-box">
                <div class="card-box-title">
                    <i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send Promo to Agents
                </div>
                <div class="card-box-body">
                    <form id="send-promo-email-{{ $promo->id }}" action="/fsend-promo-email-to-agent-{{ $promo->id }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12 m-b-18">
                                <div class="card-ptext-margin">
                                    <div class="card-ptext-content">
                                        <div class="ptext-title">Promo</div>
                                        <div class="ptext-value">{{ $promo->name }}</div>
                                        <div class="ptext-title">Room</div>
                                        <div class="ptext-value">{{ $promo->rooms->rooms }}</div>
                                        <div class="ptext-title">Minimum Stay</div>
                                        <div class="ptext-value">{{ $promo->minimum_stay }} Nights</div>
                                        <div class="ptext-title">Booking Period</div>
                                        <div class="ptext-value">{{ date('d M Y',strtotime($promo->book_periode_start)) }} - {{ date('d M Y',strtotime($promo->book_periode_end)) }}</div>
                                        <div class="ptext-title">Stay Period</div>
                                        <div class="ptext-value">{{ date('d M Y',strtotime($promo->periode_start)) }} - {{ date('d M Y',strtotime($promo->periode_end)) }}</div>
                                        <div class="ptext-title">Benefits</div>
                                        <div class="ptext-value">{!! $promo->benefits ?? "-" !!}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">Title (English)</label>
                                    <input type="text" name="title" id="title" wire:model="title" class="form-control  @error('title') is-invalid @enderror" placeholder="Insert Title" value="Don't miss out on our exciting promo, book now and enjoy special offers!">
                                    @error('title')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title_mandarin">Title (Mandarin)</label>
                                    <input type="text" name="title_mandarin" id="title_mandarin" wire:model="title_mandarin" class="form-control  @error('title_mandarin') is-invalid @enderror" placeholder="Insert Title" value="別錯過我們精彩的優惠活動，立即預訂，尊享專屬優惠！">
                                    @error('title_mandarin')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suggestion">Suggestion (English)</label>
                                    <textarea id="suggestion" name="suggestion"  class="textarea_editor form-control border-radius-0 @error('suggestion') is-invalid @enderror" placeholder="Insert some text ...">Enjoy our exclusive promotion available for a limited time only, carefully curated to bring you the very best in comfort, value, and experience.</textarea>
                                    @error('suggestion')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suggestion_mandarin">Suggestion (Mandarin)</label>
                                    <textarea id="suggestion_mandarin" name="suggestion_mandarin"  class="textarea_editor form-control border-radius-0 @error('suggestion_mandarin') is-invalid @enderror" placeholder="Insert some text ...">把握限時專屬優惠，為您精心打造極致舒適、超值體驗與難忘回憶！</textarea>
                                    @error('suggestion_mandarin')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <input type="hidden" name="link" value="https://online.balikamitour.com/hotel-{{ $hotel->code }}">
                    </form>
                </div>
                <div class="card-box-footer">
                    <button type="submit" form="send-promo-email-{{ $promo->id }}" class="btn btn-primary"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i> Send Promo</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>