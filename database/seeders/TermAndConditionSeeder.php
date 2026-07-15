<?php

namespace Database\Seeders;

use App\Models\TermAndCondition;
use Illuminate\Database\Seeder;

class TermAndConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = [
            [
                'name_id' => 'Bagaimana cara mendaftar di sistem?',
                'name_en' => 'How do I register on the system?',
                'name_zh' => '如何在系统中注册？',
                'policy_id' => '<p>Untuk memulai proses kemitraan, siapkan profil perusahaan, izin usaha, NPWP, dan dokumen pendukung bisnis. Kirim dokumen tersebut ke e-admin@balikamitour.com dengan subjek "New Agent Registration Request" dan tim kami akan meninjau pengajuan Anda.</p>',
                'policy_en' => '<p>To begin the partnership process, please prepare your company profile, business license, tax identification number, and supporting business documents. Send them to e-admin@balikamitour.com with the subject line "New Agent Registration Request" and our team will review your submission.</p>',
                'policy_zh' => '<p>要开始合作伙伴申请流程，请准备公司资料、营业执照、税务识别号及其他业务支持文件。请将文件发送至 e-admin@balikamitour.com，邮件主题为 "New Agent Registration Request"，我们的团队将审核您的申请。</p>',
            ],
            [
                'name_id' => 'Siapa yang dapat menjadi partner agent Bali Kami Tour?',
                'name_en' => 'Who can become a partner agent of Bali Kami Tour?',
                'name_zh' => '谁可以成为 Bali Kami Tour 的合作代理？',
                'policy_id' => '<p>Travel agency berizin, tour operator, wholesaler, dan corporate travel manager dapat mengajukan kerja sama. Setiap aplikasi akan ditinjau agar kemitraan sesuai secara komersial dan profesional.</p>',
                'policy_en' => '<p>Licensed travel agencies, tour operators, wholesalers, and corporate travel managers are welcome to apply. We review each application to make sure the partnership is commercially aligned and professionally managed.</p>',
                'policy_zh' => '<p>持牌旅行社、旅游运营商、批发商和企业差旅经理均可申请。我们会审核每项申请，以确保合作关系符合商业方向并具备专业管理能力。</p>',
            ],
            [
                'name_id' => 'Berapa lama proses persetujuan registrasi?',
                'name_en' => 'How long does the registration approval process take?',
                'name_zh' => '注册审批流程需要多长时间？',
                'policy_id' => '<p>Setelah dokumen yang dibutuhkan lengkap, proses persetujuan biasanya membutuhkan waktu 2 sampai 5 hari kerja. Kami akan mengirimkan pembaruan melalui email setelah proses review selesai.</p>',
                'policy_en' => '<p>Once the required documents are complete, approval usually takes around 2 to 5 business days. We will update you by email as soon as the review is complete.</p>',
                'policy_zh' => '<p>所需文件完整后，审批通常需要 2 到 5 个工作日。审核完成后，我们会通过电子邮件通知您。</p>',
            ],
            [
                'name_id' => 'Bagaimana cara mengakses promosi hotel dan harga khusus?',
                'name_en' => 'How do I access hotel promotions and special rates?',
                'name_zh' => '如何查看酒店促销和特别价格？',
                'policy_id' => '<p>Setelah akun disetujui, Anda akan mendapatkan akses ke partner system untuk melihat promosi aktif, harga khusus, dan ketersediaan inventory dalam satu platform.</p>',
                'policy_en' => '<p>After your account is approved, you will receive access to the partner system where live promotions, special rates, and available inventory can be reviewed in one place.</p>',
                'policy_zh' => '<p>账号获批后，您将获得合作伙伴系统访问权限，可在同一平台查看实时促销、特别价格和可用库存。</p>',
            ],
            [
                'name_id' => 'Dukungan seperti apa yang tersedia untuk agent?',
                'name_en' => 'What kind of support is available for agents?',
                'name_zh' => '代理可以获得哪些支持？',
                'policy_id' => '<p>Tim kami siap membantu koordinasi booking, pertanyaan operasional, panduan platform, dan klarifikasi layanan ketika tim Anda membutuhkan bantuan yang cepat dan dapat diandalkan.</p>',
                'policy_en' => '<p>Our team is available to support booking coordination, operational questions, platform guidance, and service clarification whenever your team needs quick and reliable assistance.</p>',
                'policy_zh' => '<p>当您的团队需要快速可靠的协助时，我们的团队可支持预订协调、运营问题、平台指导和服务说明。</p>',
            ],
            [
                'name_id' => 'Apakah saya dapat melacak riwayat booking dan invoice melalui sistem?',
                'name_en' => 'Can I track booking history and invoices through the system?',
                'name_zh' => '我可以通过系统追踪预订历史和发票吗？',
                'policy_id' => '<p>Ya. Partner yang telah disetujui dapat melihat aktivitas booking, memantau reservasi aktif, dan mengakses catatan invoice langsung dari platform.</p>',
                'policy_en' => '<p>Yes. Approved partners can review booking activity, monitor active reservations, and access invoice records directly from the platform.</p>',
                'policy_zh' => '<p>可以。获批合作伙伴可直接在平台中查看预订活动、跟进有效预订并访问发票记录。</p>',
            ],
        ];

        foreach ($faqs as $faq) {
            TermAndCondition::updateOrCreate(
                [
                    'type' => 'FAQ',
                    'name_en' => $faq['name_en'],
                ],
                array_merge($faq, [
                    'type' => 'FAQ',
                    'status' => 'Active',
                ])
            );
        }
    }
}
