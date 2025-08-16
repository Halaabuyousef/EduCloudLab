@extends('pages.app')
@section('title')
@section('content')
<section class="register-section">
    <div class="register-container">
        <div class="register-form-container">
            <div class="register-header">
                <h1>إنشاء حساب جديد</h1>
                <p>املأ البيانات التالية لإنشاء حسابك</p>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-steps">
                    <div class="progress-bar" id="progressBar"></div>
                    <div class="progress-step active" data-title="نوع الحساب">
                        <span>1</span>
                    </div>
                    <div class="progress-step" data-title="المعلومات الشخصية">
                        <span>2</span>
                    </div>
                    <div class="progress-step" data-title="المعلومات المهنية">
                        <span>3</span>
                    </div>
                    <div class="progress-step" data-title="التحقق من الهوية">
                        <span>4</span>
                    </div>
                </div>
            </div>

            <form id="registerForm" method="POST" action="{{ route($guard.'.register.submit') }} " enctype="multipart/form-data">
                @csrf
                <div class="form-steps">
                    <!-- Step 1: Account Type -->
                    <div class="form-step active" id="step1">
                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> نوع الحساب</label>
                            <div class="account-type">
                                <input type="radio" name="accountType" id="client" value="user" checked>
                                <label for="client">
                                    <i class="fas fa-briefcase"></i>
                                    <span>عميل</span>
                                    <small>أبحث عن محترفين لتنفيذ مشاريعي</small>
                                </label>

                                <input type="radio" name="accountType" id="freelancer" value="freelancer">
                                <label for="freelancer">
                                    <i class="fas fa-laptop-code"></i>
                                    <span>فريلانسر</span>
                                    <small>أقدم خدماتي في مجال خبرتي</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Personal Information -->
                    <div class="form-step" id="step2">
                        <div class="form-group">
                            <label for="fullname"><i class="fas fa-user"></i> الاسم الكامل</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input name="fullname" type="text" id="fullname" class="form-control" placeholder="ادخل اسمك الكامل"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="email" id="email" class="form-control"
                                    placeholder="ادخل بريدك الإلكتروني" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> كلمة المرور</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" id="password" class="form-control"
                                    placeholder="أنشئ كلمة مرور قوية" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword"><i class="fas fa-lock"></i> تأكيد كلمة المرور</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                    placeholder="أعد إدخال كلمة المرور" required>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Professional Information -->
                    <div class="form-step" id="step3">
                        <div class="form-group">
                            <h3><i class="fas fa-tags"></i> اختر مجالات تخصصك</h3>
                            <div class="categories-grid">
                                <div class="category-item">
                                    <input name="skill-webDev" type="checkbox" id="webDev">
                                    <label for="webDev">تطوير الويب</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="mobileApp">
                                    <label for="mobileApp">تطبيقات الموبايل</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="graphicDesign">
                                    <label for="graphicDesign">تصميم جرافيك</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="contentWriting">
                                    <label for="contentWriting">كتابة المحتوى</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="marketing">
                                    <label for="marketing">التسويق الرقمي</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="translation">
                                    <label for="translation">الترجمة</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="videoEditing">
                                    <label for="videoEditing">مونتاج الفيديو</label>
                                </div>
                                <div class="category-item">
                                    <input type="checkbox" id="seo">
                                    <label for="seo">تحسين محركات البحث</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bio"><i class="fas fa-user-edit"></i> نبذة عنك</label>
                            <textarea id="bio" class="form-control" rows="4"
                                placeholder="اكتب نبذة مختصرة عنك وخبراتك المهنية"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="experience"><i class="fas fa-chart-line"></i> سنوات الخبرة</label>
                            <select id="experience" class="form-control">
                                <option value="">اختر عدد سنوات الخبرة</option>
                                <option value="0-1">أقل من سنة</option>
                                <option value="1-3">1-3 سنوات</option>
                                <option value="3-5">3-5 سنوات</option>
                                <option value="5+">5 سنوات فأكثر</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 4: Identity Verification -->
                    <div class="form-step" id="step4">
                        <div class="verification-steps">
                            <h3><i class="fas fa-id-card"></i> إثبات الهوية</h3>

                            <!-- Scrollable container -->
                            <div class="verification-steps-container">
                                <div class="scroll-indicator">
                                    <i class="fas fa-chevron-down"></i> قم بالتمرير لأسفل لإكمال جميع الخطوات
                                </div>

                                <div class="step active">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <div class="step-title">رفع صورة الهوية</div>
                                        <p>قم بتحميل صورة واضحة من وثيقة هوية رسمية (بطاقة شخصية، جواز سفر، رخصة
                                            قيادة)</p>
                                        <div class="upload-area" id="idUploadArea">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>انقر أو اسحب الملف هنا</p>
                                            <small>JPG, PNG, PDF - الحد الأقصى للحجم 5MB</small>
                                        </div>
                                        <input type="file" id="idUpload" accept=".jpg,.jpeg,.png,.pdf"
                                            style="display:none;">
                                        <div class="upload-preview" id="idPreview"></div>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <div class="step-title">صورة الهوية مع صاحبها</div>
                                        <p>قم بتحميل صورة تظهر وجهك واضحًا مع وثيقة الهوية التي قمت بتحميلها في
                                            الخطوة السابقة</p>
                                        <div class="upload-area" id="selfieUploadArea">
                                            <i class="fas fa-camera"></i>
                                            <p>انقر أو اسحب الملف هنا</p>
                                            <small>JPG, PNG - الحد الأقصى للحجم 5MB</small>
                                        </div>
                                        <input type="file" id="selfieUpload" accept=".jpg,.jpeg,.png"
                                            style="display:none;">
                                        <div class="upload-preview" id="selfiePreview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-nav btn-prev" id="prevBtn" disabled>
                        السابق <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn-nav btn-next" id="nextBtn">
                        التالي <i class="fas fa-arrow-left"></i>
                    </button>
                    <button type="submit" class="btn-register" id="submitBtn" style="display:none;">
                        <i class="fas fa-user-plus"></i> إنشاء حساب جديد
                    </button>
                </div>

                <div class="signin-link">
                    لديك حساب بالفعل؟ <a href="{{ route($guard.'.indexLogin') }}">تسجيل الدخول</a>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection