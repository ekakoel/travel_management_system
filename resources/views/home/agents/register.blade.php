@extends('layouts.app')

@section('title', 'Register as Agent')

@section('content')
<div class="container">
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
    <h2 class="mb-4">Agent Registration</h2>
    {{-- Terms and Conditions --}}
    @if (session('terms'))
        <div class="alert alert-info">
            {!! session('terms') !!}
        </div>
    @endif
    <form method="POST" action="{{ route('agent.register.submit') }}" enctype="multipart/form-data">
        @csrf

        <!-- Company Info -->
        <input class="form-control mb-2" type="text" name="company_name" placeholder="Company Name" required>
        <input class="form-control mb-2" type="text" name="pic_name" placeholder="Person in Charge (PIC)" required>
        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-2" type="text" name="phone" placeholder="Phone Number" required>

        <select class="form-control mb-2" name="country" required>
            <option value="">Select Country</option>
            <option>Indonesia</option>
            <option>China</option>
            <option>Taiwan</option>
            <option>Others</option>
        </select>

        <input class="form-control mb-2" type="text" name="company_address" placeholder="Company Address" required>
        <input class="form-control mb-2" type="url" name="website" placeholder="Company Website (Optional)">

        <!-- Document Uploads -->
        <div class="mb-3">
            <label for="business_license" class="form-label">Business License <span class="text-danger">*</span></label>
            <input type="file" name="business_license" id="business_license" 
                   class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
        </div>
        
        <div class="mb-3">
            <label for="tax_document" class="form-label">Tax Document</label>
            <input type="file" name="tax_document" id="tax_document" 
                   class="form-control" accept=".pdf, .jpg, .jpeg, .png">
        </div>
        
        <div class="mb-3">
            <label for="company_letter" class="form-label">Company Letter <span class="text-danger">*</span></label>
            <input type="file" name="company_letter" id="company_letter" 
                   class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
        </div>
        
        <div class="mb-3">
            <label for="translation_documents" class="form-label">Translation Documents (if any)</label>
            <input type="file" name="translation_documents[]" id="translation_documents" 
                   class="form-control" accept=".pdf, .jpg, .jpeg, .png" multiple>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" {{ old('agree_terms') ? 'checked' : '' }} required>
            <label class="form-check-label" for="agree_terms">
                I have read and agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>.
            </label>
        </div>
        
        @if ($errors->has('agree_terms'))
            <div class="text-danger">
                {{ $errors->first('agree_terms') }}
            </div>
        @endif
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <!-- Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
            </div>
            <div class="modal-body">
                <p>By registering as a travel agent partner with <strong>Bali Kami Tour</strong>, you agree to the following terms and conditions:</p>
                
                <ol>
                    <li><strong>Eligibility:</strong> Only legally registered travel agencies or corporate travel companies are eligible to register. Individuals or personal accounts will not be accepted.</li>
            
                    <li><strong>Required Documents:</strong> You are required to upload valid and up-to-date documents including:
                        <ul>
                            <li>Business License (SIUP or equivalent)</li>
                            <li>Company Cooperation Letter or Letter of Intent</li>
                            <li>Tax Registration Document (optional)</li>
                            <li>Certified translations if the documents are not in English or Indonesian</li>
                        </ul>
                    </li>
            
                    <li><strong>File Format:</strong> All documents must be uploaded in one of the following formats: <strong>PDF, JPG, JPEG, PNG</strong>. Maximum file size: 2MB per document.</li>
            
                    <li><strong>Approval Process:</strong> Submitted applications will undergo a verification process and may take 2–5 business days for review. We reserve the right to approve or reject any registration request without obligation to provide detailed explanation.</li>
            
                    <li><strong>Data Usage:</strong> All data submitted will be stored securely and used only for internal verification and communication purposes. We do not share your data with third parties without your consent, unless required by law.</li>
            
                    <li><strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your login credentials. Any activity that occurs under your account will be your responsibility.</li>
            
                    <li><strong>Accuracy of Information:</strong> You affirm that all information and documents provided are true, accurate, and not misleading. Providing false information may result in permanent disqualification from our system.</li>
            
                    <li><strong>Prohibited Activities:</strong> Any misuse of the system, including attempts to impersonate other companies, abuse of our services, or illegal activities, will result in immediate suspension and legal consequences if necessary.</li>
            
                    <li><strong>Termination:</strong> Bali Kami Tour reserves the right to terminate or suspend any account at its discretion if it is found to be in violation of any terms or policies.</li>
            
                    <li><strong>Modification of Terms:</strong> These terms and conditions may be updated from time to time without prior notice. The latest version will always be available on our website or system login page.</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="acceptTermsBtn" data-bs-dismiss="modal">Accept</button>
            </div>
        </div>
        </div>
    </div>
</div>
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top" style="display: block;"><i class="bi bi-arrow-up"></i></a>
<script>
    document.getElementById('acceptTermsBtn').addEventListener('click', function () {
        document.getElementById('agree_terms').checked = true;
    });
</script>
@endsection
