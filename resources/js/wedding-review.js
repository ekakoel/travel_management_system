    const checkbox = document.getElementById('agreeTerms');
    const submitBtn = document.getElementById('submitBtn');

    checkbox.addEventListener('change', function () {
        submitBtn.disabled = !this.checked;
    });

    const translations = {
        "en": {
            "submit": "Submit Review",
            "successTitle": "🌟 We appreciate your feedback!",
            "successBody": "Your voice matters. Thank you for helping us become better, one review at a time.",
            "errorTitle": "❌ Failed to send your review",
            "errorBody": "Please check again! The required fields are not filled in"
        },
        // TRADITIONAL
        "zh-TW": {
            "submit": "提交評價",
            "successTitle": "🌟 感謝您的回饋！",
            "successBody": "您的意見對我們非常重要。感謝您幫助我們變得更好！",
            "errorTitle": "❌ 無法提交您的評論",
            "errorBody": "請再次確認！有必填欄位尚未填寫"
        },
        // SIMPLIFIED
        "zh-CN": {
            "submit": "提交评价",
            "successTitle": "🌟 感谢您的反馈！",
             "successBody": "您的意见对我们很重要。感谢您帮助我们不断进步！",
            "errorTitle": "❌ 无法提交您的评价",
            "errorBody": "请再次确认！有必填项尚未填写"
        }
        
    };

    function getCurrentLang() {
        const lang = document.documentElement.lang;
        if (lang === 'zh-CN' || lang === 'zh-TW' || lang === 'en') {
        return lang;
        }
        return 'en';
    }

    function validateRatings() {
        const lang = getCurrentLang();
        const t = translations[lang] || translations['en'];

        const ratingNames = [
            'communication_effeciency',
            'workflow_planning',
            'material_preparation',
            'service_attitude',
            'execution_of_workflow',
            'time_management',
            'guest_care',
            'team_coordination',
            'third_party_coordination',
            'problem_solving_ability',
            'wrap_up_and_item_check',
            'couple_mood',
            'customer_name',
        ];
        let isValid = true;

        ratingNames.forEach(name => {
        const inputs = document.querySelectorAll(`input[name="${name}"]`);
        const isChecked = [...inputs].some(input => input.checked);
        const errorEl = document.getElementById(`error-${name}`);

        if (errorEl) {
            if (!isChecked) {
            errorEl.textContent = t[`error_${name}`] || 'This field is required';
            errorEl.style.display = 'block';
            isValid = false;
            } else {
            errorEl.style.display = 'none';
            }
        }
        });
        return isValid;
    }

  async function handleReviewSubmit(e) {
    e.preventDefault();
    const form = e.target;

    // 🔒 Jalankan validasi rating dulu sebelum submit
    if (!validateRatings()) {
      return; // Stop proses submit jika tidak valid
    }

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());
    const lang = getCurrentLang();
    const t = translations[lang] || translations['en'];

    const notificationBox = document.getElementById('notificationBox');
    notificationBox.classList.add('d-none');

    try {
      const response = await fetch('https://online.balikamitour.com/api/submit-wedding-review', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const result = await response.json();

      if (response.ok) {
        // notificationBox.innerHTML = `<strong>${t.successTitle}</strong><br>${t.successBody}`;
        // notificationBox.className = 'alert alert-success mt-3';
        form.reset();

        // Sembunyikan semua pesan error setelah reset
        const errorEls = document.querySelectorAll('.invalid-feedback');
        errorEls.forEach(el => el.style.display = 'none');

        console.log("Redirecting to thankyou.html...");
        window.location.href = '/thankyou.html';  // Ganti dengan URL halaman thank you kamu

      } else {
        throw new Error(result.message || 'Error');
        if (result.message === 'Review limit reached.') {
            responseContainer.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center" role="alert" style="font-size: 1.2rem; border-left: 5px solid #ffc107;">
                <svg xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0; width: 24px; height: 24px; margin-right: 12px;" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                <div>
                <strong>Review limit reached.</strong><br>
                You have already submitted the maximum number of reviews allowed for this booking.
                </div>
            </div>
            `;
        } else {
            responseContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                Failed to submit review. Please try again later.
            </div>
            `;
        }
      }

    } catch (err) {
      notificationBox.innerHTML = `<strong>${t.errorTitle}</strong><br>${t.errorBody}`;
      notificationBox.className = 'alert alert-danger mt-3';
    }

    notificationBox.classList.remove('d-none');
  }

  // 🚀 Aktifkan event listener setelah DOM siap
  document.addEventListener('DOMContentLoaded', function () {
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
      reviewForm.addEventListener('submit', handleReviewSubmit);
    }
  });

  const fullTranslations = {
    "en": {
        "your_detail": "Your Details",
        "wedding_organizer": "Wedding Organizer",
        "wedding_date": "Wedding Date",
        "before_the_wedding": "Before the Wedding",
        "communication_efficiency": "Communication Efficiency",
        "workflow_planning": "Workflow Planning",
        "material_preparation": "Materials Preparation",
        "on_the_wedding_day": "On the Wedding Day",
        "service_attitude": "Service Attitude",
        "execution_of_workflow": "Execution of Workflow",
        "time_management": "Time Management",
        "guest_care": "Guest Care",
        "team_coordination": "Team Coordination",
        "third_party_coordination": "Third Party Coordination",
        "problem_solving_ability": "Problem Solving Ability",
        "wrap_up_and_item_check": "Wrap Up and Item Check",
        "mood": "Mood",
        "guide_name": "Guide Name",
        "time_control": "Time Control",
        "knowledge": "Knowledge of culture & history",
        "explanation": "Explanation",
        "departure_date": "Departure Date",
        "driver_name": "Driver Name",
        "transportation": "Transportation",
        "driver": "Driver",
        "accommodation": "Accommodation",
        "meals": "Meals",
        "tour_sites": "Tour Sites",
        "tour_guide_service": "Tour Guide Service",
        "submit": "Submit Review",
        "agree_terms": "I agree to the Terms and Conditions and Privacy Policy *",
        "services": "Services",
        "service_evaluation": "Service Evaluation",
        "travel_mood": "Travel Mood",
        "very_satisfied_mood": "Very Satisfied",
        "satisfied_mood": "Satisfied",
        "normal_mood": "Neutral",
        "need_improve_mood": "Need Improvement",
        "customer_name": "Your Name",
        "i_agree_to_the": "I agree to the",
        "terms_and_conditions": "Terms and Conditions",
        "and": "and",
        "privacy_policy": "Privacy Policy",
        "submit_review": "Submit Review",
        "your_full_review": "Your Full Review",
        "tourist_details": "Tourist Details",
        "tour_details": "Tour Details",

        "driver_title": "Driver Service",
        "transportation_cleanliness": "Cleanliness",
        "transportation_air_condition": "Air Conditioner",
        "attitude": "Service Attitude",
        "driver_punctuality": "Punctuality",
        "driver_driving_skills": "Driving Skills",
        "driver_neatness": "Neatness",
        "guide_neatness": "Neatness",
        "communication": "Communication",
        "check_terms": "Please agree to the Terms and Conditions before submitting.",

        intro1: "Thank you for choosing us as your wedding planner in Bali, in order to 'BE BETTER', We must also continuously improve the service quality, we sincerely need your kind advise on the following questionnaire.",
        intro2: "Please fill out this review form to help us evaluate and improve the service of our team. Your feedback on our team and facilities is essential to ensure the best experience for all future guests.",
        intro3: "All required fields are marked with a *.",
        intro4: "Thank you for your time and support!",

        tac_head: "By submitting this review, you agree to the following terms and conditions",
        tac_li1: "Your feedback is based on a real experience.",
        tac_li2: "We may use your input for service improvements.",
        tac_li3: "Personal data is handled confidentially.",
        tac_li4: "You agree that no compensation was promised for your review.",
        tac_li5: "We may edit or reject inappropriate content.",
        tac_li6: "By continuing, you accept these terms.",
        pp_heading: "We collect your review information to improve our services. Data collected includes",
        pp_li1: "Your name",
        pp_li2: "Your feedback",
        pp_li3: "All data is confidential, not shared with third parties, and may be retained unless deletion is requested.",
        pp_li4: "By submitting, you agree to this policy.",
        questionnaire: "Questionnaire",
    },
    // TRADITIONAL
    "zh-TW": {
        "your_detail": "你的詳細資訊",
        "wedding_organizer": "婚禮策劃",
        "wedding_date": "婚禮日期",
        "before_the_wedding": "婚禮前",
        "communication_efficiency": "溝通效率",
        "workflow_planning": "流程安排",
        "material_preparation": "資料準備",
        "on_the_wedding_day": "婚禮當天",
        "service_attitude": "服務態度",
        "execution_of_workflow": "流程執行",
        "time_management": "時間把控",
        "guest_care": "賓客照顧",
        "team_coordination": "團隊分工",
        "third_party_coordination": "第三方對接",
        "problem_solving_ability": "應變能力",
        "wrap_up_and_item_check": "收尾清點",
        "departure_date": "離開日期",
        "tour_guide_service": "導遊服務",
        "time_control": "時間控制",
        "knowledge": "文化和歷史知識",
        "explanation": "解釋",
        "service_evaluation": "服務評估",
        "tour_sites": "旅遊景點",
        "your_full_review": "您的完整評價",
        "customer_name": "您的姓名",
        "i_agree_to_the": "我同意",
        "and": "和",
        "submit_review": "提交評價",
        "travel_mood": "旅行心情",
        "very_satisfied_mood": "非常滿意",
        "satisfied_mood": "滿意",
        "normal_mood": "尚可",
        "need_improve_mood": "需要改進",
        "tourist_details": "遊客資料",
        "tour_details": "行程詳情",
        "transportation_cleanliness": "車輛清潔度",
        "transportation_air_condition": "空調狀況",
        "attitude": "服務態度",
        "driver_punctuality": " 準時",
        "driver_driving_skills": "駕駛技術",
        "communication": "溝通",
        "check_terms": "請在提交前同意條款與條件。",
        "guide_name": "導遊姓名",
        "driver_name": "司機姓名",
        "transportation": "交通",
        "driver": "司機",
        "accommodation": "住宿",
        "meals": "餐飲",
        "submit": "提交",
        "terms_and_conditions": "條款與細則",
        "privacy_policy": "隱私權政策",
        "driver_title": "司機服務",
        "driver_neatness": "穿著整齊度",
        "guide_neatness": "穿著整齊度",


        intro1: "感謝您選擇我們作為您在峇里島的婚禮策劃師。為了「做得更好」，我們必須不斷提升服務品質，真誠地希望您能就以下問卷提供寶貴的建議。",
        intro2: "請填寫此評價表，以幫助我們評估和改進團隊的服務。您對我們團隊和設施的反饋對確保所有未來賓客獲得最佳體驗至關重要。",
        intro3: "所有必填欄位皆以 * 標示。",
        intro4: "感謝您的寶貴時間與支持！",
        tac_head: "提交此評價即表示您同意以下條款與條件",
        tac_li1: "您的回饋是基於真實的體驗",
        tac_li2: "我們可能會使用您的回饋來改進服務。",
        tac_li3: "個人資料將被保密處理。",
        tac_li4: "您同意您的評論沒有承諾任何賠償。",
        tac_li5: "我們可能會編輯或拒絕不適當的內容。",
        tac_li6: "繼續即表示您接受這些條款。",
        pp_heading: "我們收集您的評論信息以改善我們的服務。收集的數據包括：",
        pp_li1: "你的名字",
        pp_li2: "你的反饋",
        pp_li3: "所有數據均為機密，未與第三方共享，除非要求刪除，否則可能會被保留。",
        pp_li4: "提交即表示您同意此政策。",
        questionnaire: "意見調查表",
    },
    // SIMPLIFIED
    "zh-CN": {
        "your_detail": "你的详细信息",
        "wedding_organizer": "婚礼策划",
        "wedding_date": "婚礼日期",
        "before_the_wedding": "婚礼前",
        "communication_efficiency": "沟通效率",
        "workflow_planning": "流程安排",
        "material_preparation": "资料准备",
        "on_the_wedding_day": "婚礼当天",
        "service_attitude": "服务态度",
        "execution_of_workflow": "流程执行",
        "time_management": "时间把控",
        "guest_care": "宾客照顾",
        "team_coordination": "团队分工",
        "third_party_coordination": "第三方对接",
        "problem_solving_ability": "应变能力",
        "wrap_up_and_item_check": "收尾清点",
        "departure_date": "离开日期",
        "tour_guide_service": "导游服务",
        "time_control": "时间控制",
        "knowledge": "文化和历史知识",
        "explanation": "解释",
        "service_evaluation": "服务评估",
        "tour_sites": "旅游景点",
        "your_full_review": "您的完整评价",
        "customer_name": "您的姓名",
        "i_agree_to_the": "我同意",
        "and": "和",
        "submit_review": "提交评价",
        "travel_mood": "旅行心情",
        "very_satisfied_mood": "非常满意",
        "satisfied_mood": "满意",
        "normal_mood": "尚可",
        "need_improve_mood": "需要改进",
        "tourist_details": "游客资料",
        "tour_details": "行程详情",
        "transportation_cleanliness": "车辆清洁度",
        "transportation_air_condition": "空调状况",
        "attitude": "服务态度",
        "driver_punctuality": "准时",
        "driver_driving_skills": "驾驶技术",
        "communication": "沟通",
        "check_terms": "请在提交前同意条款与条件。",
        "guide_name": "导游姓名",
        "driver_name": "司机姓名",
        "transportation": "交通",
        "driver": "司机",
        "accommodation": "住宿",
        "meals": "餐饮",
        "submit": "提交",
        "terms_and_conditions": "条款与细则",
        "privacy_policy": "隐私政策",
        "driver_title": "司机服务",
        "driver_neatness": "穿着整齐度",
        "guide_neatness": "穿着整齐度",


        intro1: "感谢您选择我们作为您在巴厘岛的婚礼策划师。为了“做得更好”，我们必须不断提升服务质量，真诚地希望您能就以下问卷提供宝贵的建议。",
        intro2: "请填写此评价表，以帮助我们评估和改进团队的服务。您对我们团队和设施的反馈对确保所有未来宾客获得最佳体验至关重要。",
        intro3: "所有必填字段均以 * 标注。",
        intro4: "感谢您的宝贵时间和支持！",
        tac_head: "提交此评价即表示您同意以下条款和条件",
        tac_li1: "您的反馈基于真实的体验",
        tac_li2: "我们可能会使用您的反馈来改进服务。",
        tac_li3: "个人数据将被保密处理",
        tac_li4: "您同意您的评论没有承诺任何赔偿。",
        tac_li5: "我们可能会编辑或拒绝不适当的内容。",
        tac_li6: "继续即表示您接受这些条款。",
        pp_heading: "我们收集您的评论信息以改善我们的服务。收集的数据包括：",
        pp_li1: "你的名字",
        pp_li2: "你的反馈",
        pp_li3: "所有数据均为机密，未与第三方共享，除非要求删除，否则可能会被保留。",
        pp_li4: "提交即表示您同意此政策。",
        questionnaire: "意见调查表",
        
    }
  };

  function translate(lang) {
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (fullTranslations[lang] && fullTranslations[lang][key]) {
        el.textContent = fullTranslations[lang][key];
      }
    });
  }

  document.getElementById('languageSelector').addEventListener('change', function () {
    const selectedLang = this.value;
    document.documentElement.lang = selectedLang;
    translate(selectedLang);
  });

  // Default: English
  document.documentElement.lang = 'en';
  translate('en');

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('reviewForm');
        const checkbox = document.getElementById('agreeTerms');
        const warningMessage = document.getElementById('warningMessage');

        form.addEventListener('submit', function (e) {
            if (!checkbox.checked) {
                e.preventDefault(); // Mencegah submit form
                warningMessage.style.display = 'block';
            } else {
                warningMessage.style.display = 'none';
            }
        });
    });