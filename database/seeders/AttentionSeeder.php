<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttentionSeeder extends Seeder
{
    public function run()
    {
        DB::table('attentions')->insert(
            [
                // ORDER ADMIN
                [
                    'page' => 'order-admin',
                    'name' => 'Active Orders',
                    'attention_en' => 'Is an order that has been validated by the admin through the validation process. Orders cannot be edited and can only change their status.',
                    'attention_zh' => '是管理员通过验证过程验证过的订单。订单无法编辑，只能更改其状态。',
                ],
                [
                    'page' => 'order-admin',
                    'name' => 'Pending Orders',
                    'attention_en' => 'Is an orders that have not been validated and are waiting to be validated.',
                    'attention_zh' => '是尚未验证且正在等待验证的订单。',
                ],
                [
                    'page' => 'order-admin',
                    'name' => 'Invalid Orders',
                    'attention_en' => 'Is an orders that do not have complete data. Orders can be edited, and validated by contacting the agent concerned.',
                    'attention_zh' => '是一个没有完整数据的订单。可以通过联系相关代理来编辑和验证订单。',
                ],
                [
                    'page' => 'order-admin',
                    'name' => 'Rejected Orders',
                    'attention_en' => 'Is an orders that do not have valid data and the agent cannot be contacted to validate the order. Rejected orders can be edited and re-validated if the agent can be contacted again.',
                    'attention_zh' => '是没有有效数据的订单，无法联系代理来验证订单。如果可以再次联系代理，则可以编辑和重新验证被拒绝的订单。',
                ],
                [
                    'page' => 'order-admin',
                    'name' => 'Archived Orders',
                    'attention_en' => 'Is an order that has been archived. Orders cannot be edited or deleted.',
                    'attention_zh' => '是已经归档的订单。无法编辑或删除订单。',
                ],
                 // ORDER USER
                [
                    'page' => 'orders',
                    'name' => 'Draft',
                    'attention_en' => 'Orders with "Draft" status are orders that have not been sent to the admin, and only you can see them, and orders in "Draft" status can also be modified before you submit them.',
                    'attention_zh' => '状态为 “草稿” 的订单是尚未发送给管理员的订单，只有您可以看到，状态为“草稿”的订单也可以在提交前进行修改。',
                ],
                [
                    'page' => 'orders',
                    'name' => 'Pending',
                    'attention_en' => 'Orders with status "Pending" are orders that you have submitted, and are waiting for the confirmation process. The admin will contact you via email, telephone, or social media in your profile to validate the order data that you have sent.',
                    'attention_zh' => '状态为 “待处理” 的订单是您已提交并正在等待确认流程的订单。管理员将通过您个人资料中的电子邮件、电话或社交媒体与您联系，以验证您发送的订单数据。',
                ],
                [
                    'page' => 'orders',
                    'name' => 'Invalid',
                    'attention_en' => 'Orders with status "Invalid" are orders with incomplete data. Please complete your profile data such as your email and telephone number before resending the order so that the Admin can verify your order.',
                    'attention_zh' => '状态为 “无效” 的订单是数据不完整的订单。请在重新发送订单之前完成您的个人资料数据，例如您的电子邮件和电话号码，以便管理员可以验证您的订单。',
                ],
                [
                    'page' => 'orders',
                    'name' => 'Rejected',
                    'attention_en' => 'Orders with status "Rejected" Your order was rejected due to various factors, your profile data is incomplete, or we cannot contact your telephone number or email to verify your order. Please complete your profile data with actual data to simplify the verification process of your order!',
                    'attention_zh' => '状态为 “已拒绝” 的订单您的订单由于各种因素被拒绝，您的个人资料数据不完整，或者我们无法联系您的电话号码或电子邮件以验证您的订单。请使用实际数据完善您的个人资料数据，以简化您的订单验证过程！',
                ],
                [
                    'page' => 'orders',
                    'name' => 'Active',
                    'attention_en' => 'Orders with status "Active" is an order that has been validated by the admin and Guests can enjoy the services according to your order.',
                    'attention_zh' => '状态为 “有效” 的订单是已经过管理员验证的订单，客人可以根据您的订单享受服务。',
                ],
                // EDIT TOUR
                [
                    'page' => 'admin-tour-edit',
                    'name' => 'Status Active',
                    'attention_en' => 'Tours with active status are tour data that are valid and can be seen and ordered by the agent.',
                    'attention_zh' => '具有活动状态的旅游是有效的旅游数据，可以被代理人看到和订购。',
                ],
                [
                    'page' => 'admin-tour-edit',
                    'name' => 'Status Draft',
                    'attention_en' => 'Tours with draft status are tours with invalid data and cannot be seen and ordered by the agent.',
                    'attention_zh' => '具有草稿状态的游览是具有无效数据的游览，代理人无法查看和订购。',
                ],
                [
                    'page' => 'admin-tour-edit',
                    'name' => 'Status Archived',
                    'attention_en' => 'Tours with archived status are tours that are no longer marketed and the data is stored as files.',
                    'attention_zh' => '具有存档状态的旅游是不再销售的旅游，数据存储为文件。',
                ],
                [
                    'page' => 'admin-tour-edit',
                    'name' => 'Activated Tour Package',
                    'attention_en' => 'Before the tour package is activated make sure all data is correct! after the tour package is activated, the tour package can be seen and ordered by the agent.',
                    'attention_zh' => '在激活旅游套餐之前，请确保所有数据都是正确的！旅游套餐激活后，代理人可以看到和订购旅游套餐。',
                ],
                // DETAIL ADMIN HOTEL
                [
                    'page' => 'admin-hotel-detail',
                    'name' => 'Page Information',
                    'attention_en' => 'A page that displays detailed hotel information.',
                    'attention_zh' => '显示详细酒店信息的页面。',
                ],
                [
                    'page' => 'admin-hotel-detail',
                    'name' => 'Sub Service',
                    'attention_en' => 'On this page, several sub-services are available, including suites & villas, normal prices, promo prices, and package prices',
                    'attention_zh' => '该页面提供多个子服务，包括套房&别墅、正常价格、促销价格和套餐价格',
                ],
                [
                    'page' => 'admin-hotel-detail',
                    'name' => 'Actions',
                    'attention_en' => 'You can add data, change data, and delete data using the available buttons.',
                    'attention_zh' => '您可以使用可用按钮添加数据、更改数据和删除数据。',
                ],
                [
                    'page' => 'admin-hotel-detail',
                    'name' => 'Log',
                    'attention_en' => 'Every action you take will be recorded and stored in a database to make tracking easier if an error occurs in the system',
                    'attention_zh' => '您采取的每一个动作都将被记录并存储在数据库中，以便在系统发生错误时更容易跟踪',
                ],
                [
                    'page' => 'admin-order-detail',
                    'name' => "Active Orders",
                    'attention_en' => 'Is an order that has been validated by the admin through the validation process. Orders cannot be edited and can only change their status',
                    'attention_zh' => '是已由管理員通過驗證流程驗證的訂單。訂單無法編輯，只能更改其狀態',
                ],
                [
                    'page' => 'admin-order-detail',
                    'name' => "Pending Orders",
                    'attention_en' => 'Is an orders that have not been validated and are waiting to be validated',
                    'attention_zh' => '是尚未驗證並等待驗證的訂單',
                ],
                [
                    'page' => 'admin-order-detail',
                    'name' => "Invalid Orders",
                    'attention_en' => 'Is an orders that do not have complete data. Orders can be edited, and validated by contacting the agent concerned',
                    'attention_zh' => '是一個沒有完整數據的訂單。可以通過聯繫相關代理來編輯和驗證訂單。',
                ],
                [
                    'page' => 'admin-order-detail',
                    'name' => "Rejected Orders",
                    'attention_en' => 'Is an orders that do not have valid data and the agent cannot be contacted to validate the order. Rejected orders can be edited and re-validated if the agent can be contacted again',
                    'attention_zh' => '訂單沒有有效數據，無法聯繫代理商驗證訂單。如果可以再次聯繫代理，則可以編輯並重新驗證被拒絕的訂單。',
                ],
                [
                    'page' => 'admin-order-detail',
                    'name' => "Archived Orders",
                    'attention_en' => 'Is an order that has been archived. Orders cannot be edited or deleted',
                    'attention_zh' => '是已存檔的訂單。訂單無法編輯或刪除。',
                ],
            ]
        );
    }
}
