<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Page\Translation;

class PageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getPages();
        if ($items) {
            $this->safeTruncate(Page::class);
            foreach ($items as $item) {
                Page::query()->create($item);
            }
        }

        $items = $this->getPageTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }
    }

    /**
     * B2B demo pages for "Apex Precision / 傲锋精密":
     *   about / capabilities / quality / oem-odm / contact
     *
     * @return array[]
     */
    private function getPages(): array
    {
        return [
            ['id' => 1, 'slug' => 'about', 'position' => 1, 'viewed' => 689, 'active' => 1],
            ['id' => 2, 'slug' => 'capabilities', 'position' => 2, 'viewed' => 534, 'active' => 1],
            ['id' => 3, 'slug' => 'quality', 'position' => 3, 'viewed' => 421, 'active' => 1],
            ['id' => 4, 'slug' => 'oem-odm', 'position' => 4, 'viewed' => 376, 'active' => 1],
            ['id' => 5, 'slug' => 'contact', 'position' => 5, 'viewed' => 655, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getPageTranslations(): array
    {
        return [
            // ---------- about ----------
            [
                'page_id' => 1,
                'locale'  => 'zh-cn',
                'title'   => '关于我们',
                'content' => <<<'HTML'
<p><strong>傲锋精密制造有限公司（Apex Precision Manufacturing Co., Ltd.）</strong>成立于 2008 年，坐落于制造业名城东莞，是一家专注于精密零部件研发与制造的国家级高新技术企业。</p>
<p>公司现拥有 12,000㎡ 现代化厂房、员工 180 余人，其中工程技术人员 35 人。产品远销北美、欧洲、日韩与东南亚等 30 多个国家和地区，出口占比超过 60%。</p>
<p><img src="/images/demo/company/factory.jpg" class="img-fluid rounded my-3" alt="傲锋精密生产车间"></p>
<h3>发展历程</h3>
<ul>
  <li><strong>2008</strong> 公司成立，以 CNC 加工起步</li>
  <li><strong>2012</strong> 通过 ISO 9001 质量体系认证</li>
  <li><strong>2016</strong> 扩建钣金与压铸产线，转型一站式精密制造</li>
  <li><strong>2019</strong> 通过 IATF 16949 汽车行业认证</li>
  <li><strong>2022</strong> 获评国家级高新技术企业</li>
  <li><strong>2026</strong> 二期智造工厂投产，产能提升 60%</li>
</ul>
<h3>企业愿景</h3>
<p>成为全球客户值得信赖的精密制造合作伙伴，以工程服务创造价值，以数字智造引领未来。</p>
<p><img src="/images/demo/company/team.jpg" class="img-fluid rounded my-3" alt="傲锋精密工程团队"></p>
HTML,
                'meta_title'       => '关于傲锋精密｜一站式精密零部件制造商',
                'meta_description' => '傲锋精密成立于 2008 年，专注于 CNC 加工、钣金、压铸与表面处理，为全球客户提供一站式精密零部件制造服务。',
                'meta_keywords'    => '傲锋精密,Apex Precision,精密制造,CNC加工,钣金加工,压铸',
            ],
            [
                'page_id' => 1,
                'locale'  => 'en',
                'title'   => 'About Us',
                'content' => <<<'HTML'
<p><strong>Apex Precision Manufacturing Co., Ltd.</strong>, founded in 2008 and headquartered in Dongguan — a world-renowned manufacturing hub — is a national high-tech enterprise dedicated to the R&amp;D and production of precision components.</p>
<p>Today the company operates a 12,000㎡ modern facility with 180+ employees, including 35 engineers. Our products are exported to more than 30 countries and regions across North America, Europe, Japan, Korea and Southeast Asia, with exports accounting for over 60% of revenue.</p>
<p><img src="/images/demo/company/factory.jpg" class="img-fluid rounded my-3" alt="Apex Precision workshop"></p>
<h3>Milestones</h3>
<ul>
  <li><strong>2008</strong> Founded as a CNC machining shop</li>
  <li><strong>2012</strong> ISO 9001 certified</li>
  <li><strong>2016</strong> Sheet metal and die casting lines added — one-stop precision manufacturing</li>
  <li><strong>2019</strong> IATF 16949 automotive certification</li>
  <li><strong>2022</strong> Recognized as a national high-tech enterprise</li>
  <li><strong>2026</strong> Phase-II smart factory launched, capacity +60%</li>
</ul>
<h3>Our Vision</h3>
<p>To be the most trusted precision manufacturing partner for global customers — creating value through engineering services and leading the future with digital manufacturing.</p>
<p><img src="/images/demo/company/team.jpg" class="img-fluid rounded my-3" alt="Apex Precision engineering team"></p>
HTML,
                'meta_title'       => 'About Apex Precision | One-Stop Precision Parts Manufacturer',
                'meta_description' => 'Founded in 2008, Apex Precision delivers CNC machining, sheet metal, die casting and finishing services to global customers.',
                'meta_keywords'    => 'Apex Precision, precision manufacturing, CNC machining, sheet metal, die casting',
            ],

            // ---------- capabilities ----------
            [
                'page_id' => 2,
                'locale'  => 'zh-cn',
                'title'   => '制造能力',
                'content' => <<<'HTML'
<p>傲锋精密整合 CNC 加工、钣金、压铸与表面处理四大工艺，为客户提供从单件打样到十万级量产的一站式制造能力。</p>
<h3>主要设备</h3>
<table class="table table-bordered">
  <thead><tr><th>设备</th><th>数量</th><th>关键参数</th></tr></thead>
  <tbody>
    <tr><td>3轴/4轴 CNC 加工中心</td><td>43 台</td><td>行程 1500×800×600mm，精度 ±0.005mm</td></tr>
    <tr><td>五轴联动加工中心</td><td>12 台</td><td>重复定位精度 0.003mm</td></tr>
    <tr><td>CNC 数控车床/车铣复合</td><td>30 台</td><td>加工直径 0.5–500mm</td></tr>
    <tr><td>激光切割机</td><td>3 台</td><td>6kW，碳钢 ≤ 25mm</td></tr>
    <tr><td>数控折弯机</td><td>6 台</td><td>160T / 3200mm</td></tr>
    <tr><td>冷室压铸机</td><td>12 台</td><td>80–800T</td></tr>
    <tr><td>三坐标测量仪（CMM）</td><td>5 台</td><td>精度 0.0015mm</td></tr>
  </tbody>
</table>
<h3>加工材料</h3>
<ul>
  <li><strong>铝合金</strong>：6061、6063、7075、5083、ADC12、A380</li>
  <li><strong>不锈钢</strong>：303、304、316L、17-4PH、2205</li>
  <li><strong>铜合金</strong>：H59、H62、C3604、铍铜</li>
  <li><strong>钛合金</strong>：Gr2、Gr5、Gr23</li>
  <li><strong>工程塑料</strong>：POM、PEEK、PA、PC、PTFE</li>
</ul>
<h3>产能与交期</h3>
<ul>
  <li>月产能：CNC 加工件 80 万件 / 压铸件 400 万件</li>
  <li>打样交期：7–15 天</li>
  <li>量产交期：25–35 天（视订单规模）</li>
</ul>
HTML,
                'meta_title'       => '制造能力｜傲锋精密',
                'meta_description' => '85+ 台 CNC 设备、12 台五轴、钣金与压铸产线，公差 ±0.005mm，月产能 CNC 80 万件。',
                'meta_keywords'    => '制造能力,CNC设备,五轴加工,压铸产能,傲锋精密',
            ],
            [
                'page_id' => 2,
                'locale'  => 'en',
                'title'   => 'Capabilities',
                'content' => <<<'HTML'
<p>Apex Precision integrates four core processes — CNC machining, sheet metal, die casting and surface finishing — to deliver one-stop manufacturing from single prototypes to 100k-volume production.</p>
<h3>Key Equipment</h3>
<table class="table table-bordered">
  <thead><tr><th>Equipment</th><th>Qty</th><th>Key Parameters</th></tr></thead>
  <tbody>
    <tr><td>3/4-axis CNC machining centers</td><td>43</td><td>Envelope 1500×800×600mm, accuracy ±0.005mm</td></tr>
    <tr><td>Simultaneous 5-axis centers</td><td>12</td><td>Repeatability 0.003mm</td></tr>
    <tr><td>CNC lathes / mill-turn</td><td>30</td><td>Diameter 0.5–500mm</td></tr>
    <tr><td>Laser cutting machines</td><td>3</td><td>6kW, carbon steel ≤ 25mm</td></tr>
    <tr><td>CNC press brakes</td><td>6</td><td>160T / 3200mm</td></tr>
    <tr><td>Cold-chamber die casting machines</td><td>12</td><td>80–800T</td></tr>
    <tr><td>CMM measuring machines</td><td>5</td><td>Accuracy 0.0015mm</td></tr>
  </tbody>
</table>
<h3>Materials</h3>
<ul>
  <li><strong>Aluminum</strong>: 6061, 6063, 7075, 5083, ADC12, A380</li>
  <li><strong>Stainless steel</strong>: 303, 304, 316L, 17-4PH, 2205</li>
  <li><strong>Brass &amp; copper</strong>: H59, H62, C3604, beryllium copper</li>
  <li><strong>Titanium</strong>: Gr2, Gr5, Gr23</li>
  <li><strong>Engineering plastics</strong>: POM, PEEK, PA, PC, PTFE</li>
</ul>
<h3>Capacity &amp; Lead Time</h3>
<ul>
  <li>Monthly capacity: 800k CNC parts / 4M die castings</li>
  <li>Prototypes: 7–15 days</li>
  <li>Production: 25–35 days (volume dependent)</li>
</ul>
HTML,
                'meta_title'       => 'Capabilities | Apex Precision',
                'meta_description' => '85+ CNC machines, twelve 5-axis centers, sheet metal and die casting lines. Tolerance ±0.005mm, 800k CNC parts per month.',
                'meta_keywords'    => 'capabilities, CNC equipment, 5-axis machining, die casting capacity, Apex Precision',
            ],

            // ---------- quality ----------
            [
                'page_id' => 3,
                'locale'  => 'zh-cn',
                'title'   => '质量保证',
                'content' => <<<'HTML'
<p>质量是精密制造的生命线。傲锋建立了 ISO 9001、IATF 16949、ISO 13485 三体系融合的质量管理平台，覆盖来料、过程与出货全环节。</p>
<h3>体系认证</h3>
<ul>
  <li>ISO 9001:2015 质量管理体系（连续 8 年零不符合项通过审核）</li>
  <li>IATF 16949 汽车行业质量管理体系</li>
  <li>ISO 13485 医疗器械质量管理体系</li>
  <li>RoHS / REACH 合规</li>
</ul>
<h3>质量控制流程</h3>
<ul>
  <li><strong>IQC 来料检验</strong>：光谱仪材质分析（PMI）+ 硬度抽检</li>
  <li><strong>IPQC 过程检验</strong>：首件全尺寸报告，关键尺寸 SPC 监控</li>
  <li><strong>OQC 出货检验</strong>：AQL 抽样 + 外观全检</li>
  <li><strong>追溯管理</strong>：批次条码全程追溯，质量记录保存 15 年</li>
</ul>
<h3>检测设备</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>三坐标测量仪</th><td>5 台，精度 0.0015mm</td></tr>
    <tr><th>影像测量仪</th><td>3 台</td></tr>
    <tr><th>粗糙度/轮廓仪</th><td>4 台</td></tr>
    <tr><th>硬度计 / 盐雾试验箱</th><td>各 2 台</td></tr>
    <tr><th>X-Ray 探伤仪</th><td>1 台（压铸件内部缺陷检测）</td></tr>
  </tbody>
</table>
HTML,
                'meta_title'       => '质量保证｜傲锋精密',
                'meta_description' => 'ISO 9001 / IATF 16949 / ISO 13485 三体系认证，IQC/IPQC/OQC 全流程质控，批次全程追溯。',
                'meta_keywords'    => '质量保证,ISO9001,IATF16949,ISO13485,三坐标检测',
            ],
            [
                'page_id' => 3,
                'locale'  => 'en',
                'title'   => 'Quality Assurance',
                'content' => <<<'HTML'
<p>Quality is the lifeline of precision manufacturing. Apex operates an integrated quality platform spanning ISO 9001, IATF 16949 and ISO 13485, covering incoming, in-process and outgoing inspection.</p>
<h3>Certifications</h3>
<ul>
  <li>ISO 9001:2015 (passed with zero nonconformities for 8 consecutive years)</li>
  <li>IATF 16949 automotive quality management</li>
  <li>ISO 13485 medical device quality management</li>
  <li>RoHS / REACH compliant</li>
</ul>
<h3>Quality Control Workflow</h3>
<ul>
  <li><strong>IQC</strong>: spectrographic material analysis (PMI) + hardness sampling</li>
  <li><strong>IPQC</strong>: first-article full dimensional reports, SPC on critical dimensions</li>
  <li><strong>OQC</strong>: AQL sampling + 100% cosmetic inspection</li>
  <li><strong>Traceability</strong>: batch barcode tracking, records retained for 15 years</li>
</ul>
<h3>Inspection Equipment</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>CMM</th><td>5 units, accuracy 0.0015mm</td></tr>
    <tr><th>Vision measuring machines</th><td>3 units</td></tr>
    <tr><th>Roughness / profilometers</th><td>4 units</td></tr>
    <tr><th>Hardness testers / salt-spray chambers</th><td>2 each</td></tr>
    <tr><th>X-ray inspection</th><td>1 unit (internal defect detection for castings)</td></tr>
  </tbody>
</table>
HTML,
                'meta_title'       => 'Quality Assurance | Apex Precision',
                'meta_description' => 'ISO 9001 / IATF 16949 / ISO 13485 certified. Full IQC/IPQC/OQC control with batch traceability.',
                'meta_keywords'    => 'quality assurance, ISO 9001, IATF 16949, ISO 13485, CMM inspection',
            ],

            // ---------- oem-odm ----------
            [
                'page_id' => 4,
                'locale'  => 'zh-cn',
                'title'   => 'OEM/ODM 服务',
                'content' => <<<'HTML'
<p>无论是来图加工（OEM）还是联合设计开发（ODM），傲锋都以工程服务为先导，帮助客户把设计意图转化为可稳定量产的零件。</p>
<h3>合作流程</h3>
<ul>
  <li><strong>① 需求沟通</strong>：提供 2D/3D 图纸（STEP、IGS、PDF）与技术要求</li>
  <li><strong>② DFM 评审</strong>：48 小时内反馈可制造性分析与成本优化建议</li>
  <li><strong>③ 报价确认</strong>：透明报价单，含材料、工艺与检测明细</li>
  <li><strong>④ 打样验证</strong>：7–15 天交付首件，附全尺寸检测报告</li>
  <li><strong>⑤ 小批试产</strong>：验证工艺稳定性，输出 PPAP/FAI 文件</li>
  <li><strong>⑥ 量产交付</strong>：安全库存管理，支持 VMI 与滚动预测</li>
</ul>
<h3>常见问题</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>最小起订量</th><td>1 件起订（打样）</td></tr>
    <tr><th>报价周期</th><td>24 小时内</td></tr>
    <tr><th>保密协议</th><td>可签署 NDA，图纸专人管理</td></tr>
    <tr><th>付款方式</th><td>T/T，打样全款，量产 30% 定金</td></tr>
    <tr><th>出口服务</th><td>FOB 深圳 / 空运 / 国际快递</td></tr>
  </tbody>
</table>
<p>把图纸发到 <a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a>，让我们从 DFM 评审开始。</p>
HTML,
                'meta_title'       => 'OEM/ODM 服务｜傲锋精密',
                'meta_description' => '来图加工与联合开发服务：48 小时 DFM 评审、24 小时报价、7-15 天打样、PPAP 文件齐套。',
                'meta_keywords'    => 'OEM,ODM,定制加工,DFM评审,快速打样',
            ],
            [
                'page_id' => 4,
                'locale'  => 'en',
                'title'   => 'OEM/ODM Service',
                'content' => <<<'HTML'
<p>Whether build-to-print (OEM) or joint design development (ODM), Apex leads with engineering services that turn design intent into parts ready for stable mass production.</p>
<h3>Workflow</h3>
<ul>
  <li><strong>① Requirement</strong>: share 2D/3D drawings (STEP, IGS, PDF) and specifications</li>
  <li><strong>② DFM review</strong>: manufacturability analysis and cost optimization within 48 hours</li>
  <li><strong>③ Quotation</strong>: transparent quote covering material, process and inspection</li>
  <li><strong>④ Prototyping</strong>: first articles in 7–15 days with full dimensional reports</li>
  <li><strong>⑤ Pilot run</strong>: process validation with complete PPAP/FAI documentation</li>
  <li><strong>⑥ Mass production</strong>: safety stock management, VMI and rolling forecasts supported</li>
</ul>
<h3>FAQ</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>Minimum order</th><td>1 piece (prototyping)</td></tr>
    <tr><th>Quotation time</th><td>Within 24 hours</td></tr>
    <tr><th>Confidentiality</th><td>NDA available, drawings access-controlled</td></tr>
    <tr><th>Payment terms</th><td>T/T; 100% for samples, 30% deposit for production</td></tr>
    <tr><th>Shipping</th><td>FOB Shenzhen / air freight / international express</td></tr>
  </tbody>
</table>
<p>Send your drawings to <a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a> — let's start with a DFM review.</p>
HTML,
                'meta_title'       => 'OEM/ODM Service | Apex Precision',
                'meta_description' => 'Build-to-print and joint development: 48h DFM review, 24h quotation, 7–15 day prototyping, full PPAP.',
                'meta_keywords'    => 'OEM, ODM, custom machining, DFM review, rapid prototyping',
            ],

            // ---------- contact ----------
            [
                'page_id' => 5,
                'locale'  => 'zh-cn',
                'title'   => '联系我们',
                'content' => <<<'HTML'
<p>欢迎垂询报价、预约验厂或洽谈合作。我们的工程销售团队将在 24 小时内回复。</p>
<h3>联系方式</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>销售热线</th><td>+86-769-8123-4567</td></tr>
    <tr><th>邮箱</th><td><a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a></td></tr>
    <tr><th>地址</th><td>广东省东莞市长安镇高盛路 8 号 傲锋精密产业园</td></tr>
    <tr><th>工作时间</th><td>周一至周六 8:30 – 18:00（GMT+8）</td></tr>
  </tbody>
</table>
<h3>验厂与参观</h3>
<p>我们欢迎全球客户预约现场验厂。工厂距深圳宝安国际机场 40 分钟车程，可提供接机服务。</p>
<p><strong>提示：</strong>本页为 InnoCMS 演示数据，联系方式均为示例。</p>
HTML,
                'meta_title'       => '联系我们｜傲锋精密',
                'meta_description' => '联系傲锋精密：CNC 加工报价、验厂预约与合作洽谈，24 小时内响应。',
                'meta_keywords'    => '联系我们,CNC加工报价,验厂,傲锋精密',
            ],
            [
                'page_id' => 5,
                'locale'  => 'en',
                'title'   => 'Contact Us',
                'content' => <<<'HTML'
<p>Contact us for quotations, factory audits or partnership discussions. Our engineering sales team replies within 24 hours.</p>
<h3>Contact Information</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>Sales hotline</th><td>+86-769-8123-4567</td></tr>
    <tr><th>Email</th><td><a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a></td></tr>
    <tr><th>Address</th><td>No. 8 Gaosheng Road, Chang'an Town, Dongguan, Guangdong, China</td></tr>
    <tr><th>Business hours</th><td>Mon–Sat 8:30 – 18:00 (GMT+8)</td></tr>
  </tbody>
</table>
<h3>Factory Visits</h3>
<p>We welcome on-site audits from global customers by appointment. The factory is a 40-minute drive from Shenzhen Bao'an International Airport; pickup service is available.</p>
<p><strong>Note:</strong> this page is InnoCMS demo data — all contact details are examples.</p>
HTML,
                'meta_title'       => 'Contact Us | Apex Precision',
                'meta_description' => 'Contact Apex Precision for CNC machining quotes, factory audits and partnerships. Response within 24 hours.',
                'meta_keywords'    => 'contact, CNC machining quote, factory audit, Apex Precision',
            ],
        ];
    }
}
