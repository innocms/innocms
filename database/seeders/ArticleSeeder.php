<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Article\Translation;
use InnoCMS\Common\Models\ArticleTag;

class ArticleSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getArticles();
        if ($items) {
            $this->safeTruncate(Article::class);
            foreach ($items as $item) {
                Article::query()->create($item);
            }
        }

        $items = $this->getArticleTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }

        $items = $this->getArticleTags();
        if ($items) {
            $this->safeTruncate(ArticleTag::class);
            foreach ($items as $item) {
                ArticleTag::query()->create($item);
            }
        }
    }

    /**
     * @return array[]
     */
    private function getArticles(): array
    {
        $articles = [];
        foreach ($this->data() as $row) {
            $articles[] = [
                'id'         => $row['id'],
                'catalog_id' => $row['catalog_id'],
                'slug'       => $row['slug'],
                'position'   => $row['id'],
                'viewed'     => $row['viewed'],
                'author'     => $row['author'],
                'active'     => 1,
            ];
        }

        return $articles;
    }

    /**
     * @return array[]
     */
    private function getArticleTranslations(): array
    {
        $translations = [];
        foreach ($this->data() as $row) {
            foreach (['zh-cn' => 'zh', 'en' => 'en'] as $locale => $key) {
                $def            = $row[$key];
                $translations[] = [
                    'article_id'       => $row['id'],
                    'locale'           => $locale,
                    'title'            => $def['title'],
                    'summary'          => $def['summary'],
                    'image'            => $row['image'],
                    'content'          => $def['content'],
                    'meta_title'       => $locale === 'zh-cn' ? $def['title'].'｜傲锋精密' : $def['title'].' | Apex Precision',
                    'meta_description' => $def['summary'],
                    'meta_keywords'    => $locale === 'zh-cn' ? '傲锋精密,精密加工,CNC,Apex Precision' : 'Apex Precision, precision machining, CNC, OEM',
                ];
            }
        }

        return $translations;
    }

    /**
     * @return array[]
     */
    private function getArticleTags(): array
    {
        $links = [];
        $id    = 1;
        foreach ($this->data() as $row) {
            foreach ($row['tags'] as $tagId) {
                $links[] = ['id' => $id++, 'article_id' => $row['id'], 'tag_id' => $tagId];
            }
        }

        return $links;
    }

    /**
     * B2B demo content for the fictional precision manufacturer "Apex Precision / 傲锋精密".
     *
     * @return array[]
     */
    private function data(): array
    {
        return [

            // ==================== Featured products (catalog 1: products) ====================

            [
                'id'         => 1,
                'catalog_id' => 1,
                'slug'       => 'custom-cnc-machining-parts',
                'image'      => 'images/demo/products/cnc-parts.jpg',
                'viewed'     => 568,
                'author'     => 'Apex Precision',
                'tags'       => [1, 2, 3, 8],
                'zh'         => [
                    'title'   => '定制 CNC 加工件',
                    'summary' => '来图来样定制 CNC 加工件，公差可达 ±0.005mm，支持单件打样与批量生产，24 小时内快速报价。',
                    'content' => <<<'HTML'
<p>傲锋精密提供一站式定制 CNC 加工服务，根据您的 2D/3D 图纸加工铝合金、不锈钢、铜、钛合金及工程塑料零件，覆盖原型验证到量产交付的全流程。</p>
<h3>加工能力</h3>
<ul>
  <li>55+ 台 CNC 加工中心，其中 12 台五轴联动设备</li>
  <li>最大加工行程 1500 × 800 × 600 mm</li>
  <li>公差可达 ±0.005mm，表面粗糙度 Ra 0.2</li>
  <li>单件起订，打样周期 7–15 天</li>
</ul>
<h3>常用材料</h3>
<ul>
  <li>铝合金：6061、6063、7075、5083</li>
  <li>不锈钢：303、304、316L、17-4PH</li>
  <li>铜合金：H62、C3604、铍铜</li>
  <li>工程塑料：POM、PEEK、尼龙、PC</li>
</ul>
<h3>规格参数</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>设备类型</th><td>3轴 / 4轴 / 5轴 CNC 铣削、车削、车铣复合</td></tr>
    <tr><th>尺寸范围</th><td>1500 × 800 × 600 mm</td></tr>
    <tr><th>公差</th><td>±0.005mm（依图纸与材料而定）</td></tr>
    <tr><th>文件格式</th><td>STEP、IGS、X_T、PDF</td></tr>
    <tr><th>批量范围</th><td>1 件 – 100,000 件</td></tr>
    <tr><th>交期</th><td>打样 7–15 天，量产 25–35 天</td></tr>
  </tbody>
</table>
<p>欢迎发送图纸至 <a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a> 获取免费报价，我们将在 24 小时内回复。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Custom CNC Machining Parts',
                    'summary' => 'Made-to-drawing CNC machined parts with tolerances down to ±0.005mm. Prototype to mass production, quoted within 24 hours.',
                    'content' => <<<'HTML'
<p>Apex Precision delivers one-stop custom CNC machining services, producing aluminum, stainless steel, brass, titanium and engineering plastic parts from your 2D/3D drawings — from prototype validation through mass production.</p>
<h3>Capabilities</h3>
<ul>
  <li>55+ CNC machining centers, including 12 five-axis machines</li>
  <li>Maximum machining envelope 1500 × 800 × 600 mm</li>
  <li>Tolerances down to ±0.005mm, surface finish Ra 0.2</li>
  <li>No minimum order, prototypes in 7–15 days</li>
</ul>
<h3>Common Materials</h3>
<ul>
  <li>Aluminum: 6061, 6063, 7075, 5083</li>
  <li>Stainless steel: 303, 304, 316L, 17-4PH</li>
  <li>Brass &amp; copper: H62, C3604, beryllium copper</li>
  <li>Engineering plastics: POM, PEEK, Nylon, PC</li>
</ul>
<h3>Specifications</h3>
<table class="table table-bordered">
  <tbody>
    <tr><th>Equipment</th><td>3/4/5-axis CNC milling, turning, mill-turn</td></tr>
    <tr><th>Part size</th><td>Up to 1500 × 800 × 600 mm</td></tr>
    <tr><th>Tolerance</th><td>±0.005mm (drawing &amp; material dependent)</td></tr>
    <tr><th>File formats</th><td>STEP, IGS, X_T, PDF</td></tr>
    <tr><th>Volume</th><td>1 – 100,000 pcs</td></tr>
    <tr><th>Lead time</th><td>Prototype 7–15 days, production 25–35 days</td></tr>
  </tbody>
</table>
<p>Send your drawings to <a href="mailto:sales@apexprecision.cn">sales@apexprecision.cn</a> for a free quotation — we respond within 24 hours.</p>
HTML,
                ],
            ],

            [
                'id'         => 2,
                'catalog_id' => 1,
                'slug'       => 'precision-milling-parts',
                'image'      => 'images/demo/products/precision-milling.jpg',
                'viewed'     => 431,
                'author'     => 'Apex Precision',
                'tags'       => [1, 2, 5],
                'zh'         => [
                    'title'   => '精密铣削零件',
                    'summary' => '高精度 CNC 铣削加工，适合复杂型腔、薄壁与异形结构件，批量一致性好，尺寸稳定可控。',
                    'content' => <<<'HTML'
<p>精密铣削是傲锋的核心工艺之一。我们的高转速加工中心配合在线测头补偿，可稳定加工复杂型腔、深腔、薄壁（最薄 0.3mm）及异形曲面结构件。</p>
<h3>工艺优势</h3>
<ul>
  <li>主轴转速最高 20,000 rpm，镜面级精加工</li>
  <li>在线测量补偿，批量尺寸 CPK ≥ 1.33</li>
  <li>真空夹具与专用工装，解决薄壁件变形难题</li>
  <li>恒温车间（20 ± 2℃）保障测量与加工一致性</li>
</ul>
<h3>典型应用</h3>
<ul>
  <li>自动化设备结构件与安装板</li>
  <li>光学仪器支架与镜座</li>
  <li>通讯腔体与散热基座</li>
  <li>无人机机架与云台结构件</li>
</ul>
<p>支持来图定制，欢迎垂询。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Precision Milling Parts',
                    'summary' => 'High-accuracy CNC milling for complex cavities, thin walls and freeform structures with stable batch consistency.',
                    'content' => <<<'HTML'
<p>Precision milling is one of Apex's core processes. Our high-speed machining centers with on-machine probing reliably produce complex cavities, deep pockets, thin walls (down to 0.3mm) and freeform surfaces.</p>
<h3>Process Advantages</h3>
<ul>
  <li>Spindle speeds up to 20,000 rpm for mirror-grade finishing</li>
  <li>On-machine measurement compensation, batch CPK ≥ 1.33</li>
  <li>Vacuum fixtures and dedicated tooling for thin-wall stability</li>
  <li>Temperature-controlled shop (20 ± 2℃) for measurement consistency</li>
</ul>
<h3>Typical Applications</h3>
<ul>
  <li>Automation frames and mounting plates</li>
  <li>Optical instrument brackets and lens mounts</li>
  <li>Telecom cavities and heat-sink bases</li>
  <li>Drone frames and gimbal structures</li>
</ul>
<p>Made-to-drawing only. Contact us for a quotation.</p>
HTML,
                ],
            ],

            [
                'id'         => 3,
                'catalog_id' => 1,
                'slug'       => 'five-axis-machining-service',
                'image'      => 'images/demo/products/5-axis-machining.jpg',
                'viewed'     => 389,
                'author'     => 'Apex Precision',
                'tags'       => [1, 7, 8],
                'zh'         => [
                    'title'   => '五轴加工服务',
                    'summary' => '12 台五轴联动加工中心，一次装夹完成多面体复杂零件，减少装夹误差，缩短交期。',
                    'content' => <<<'HTML'
<p>五轴联动加工可在一次装夹中完成多面、多角度特征，消除重复定位误差，是叶轮、模具、医疗器械与航空结构件的理想工艺。</p>
<h3>为什么选择五轴</h3>
<ul>
  <li>一次装夹加工 5 个面，位置度精度提升 50% 以上</li>
  <li>刀具姿态优化，深腔与倒扣特征无需放电加工</li>
  <li>工序集中，交期平均缩短 30%</li>
  <li>适合钛合金、高温合金等难加工材料</li>
</ul>
<h3>设备配置</h3>
<ul>
  <li>12 台五轴加工中心（摇篮式 + 摆头式）</li>
  <li>行程 800 × 630 × 500 mm，重复定位精度 0.003mm</li>
  <li>配套 CAM 团队，支持复杂曲面编程与仿真验证</li>
</ul>
<p>如有复杂零件需要评估工艺方案，欢迎联系我们的工程团队。</p>
HTML,
                ],
                'en' => [
                    'title'   => '5-Axis Machining Service',
                    'summary' => 'Twelve simultaneous 5-axis centers complete complex multi-face parts in one setup — less error, shorter lead time.',
                    'content' => <<<'HTML'
<p>Simultaneous 5-axis machining completes multi-face, multi-angle features in a single setup, eliminating re-positioning error. It is the ideal process for impellers, molds, medical devices and aerospace structures.</p>
<h3>Why 5-Axis</h3>
<ul>
  <li>Five faces in one setup — positional accuracy improved by over 50%</li>
  <li>Optimized tool orientation removes the need for EDM on undercuts</li>
  <li>Consolidated operations cut lead times by 30% on average</li>
  <li>Well suited to titanium, Inconel and other difficult materials</li>
</ul>
<h3>Equipment</h3>
<ul>
  <li>12 five-axis centers (trunnion and swivel-head configurations)</li>
  <li>Envelope 800 × 630 × 500 mm, repeatability 0.003mm</li>
  <li>Dedicated CAM team for complex surfacing and simulation</li>
</ul>
<p>Have a complex part? Let our engineering team review the process plan.</p>
HTML,
                ],
            ],

            [
                'id'         => 4,
                'catalog_id' => 1,
                'slug'       => 'precision-turning-parts',
                'image'      => 'images/demo/products/turned-parts.jpg',
                'viewed'     => 356,
                'author'     => 'Apex Precision',
                'tags'       => [1, 2, 6],
                'zh'         => [
                    'title'   => '精密车削零件',
                    'summary' => 'CNC 车削与车铣复合加工，直径 0.5–500mm，擅长轴类、套类与接头类回转体零件。',
                    'content' => <<<'HTML'
<p>傲锋拥有 30 台 CNC 数控车床及车铣复合中心，专业加工各类回转体零件：传动轴、销轴、衬套、接头、阀体等。</p>
<h3>加工范围</h3>
<ul>
  <li>车削直径 0.5–500mm，长度 ≤ 1200mm</li>
  <li>车铣复合一次成型，同轴度 ≤ 0.01mm</li>
  <li>棒料机自动送料，适合中小批量连续生产</li>
  <li>支持螺纹、滚花、切槽等特征加工</li>
</ul>
<h3>质量控制</h3>
<ul>
  <li>首件三坐标全尺寸报告</li>
  <li>批量抽检 + 关键尺寸 100% 检测</li>
  <li>材质证明与热处理报告随货提供</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Precision Turning Parts',
                    'summary' => 'CNC turning and mill-turn machining from Ø0.5 to 500mm for shafts, bushings, fittings and other rotary parts.',
                    'content' => <<<'HTML'
<p>Apex operates 30 CNC lathes and mill-turn centers specialized in rotary parts: drive shafts, pins, bushings, fittings, valve bodies and more.</p>
<h3>Machining Range</h3>
<ul>
  <li>Turning diameter 0.5–500mm, length ≤ 1200mm</li>
  <li>Mill-turn complete machining, concentricity ≤ 0.01mm</li>
  <li>Bar feeders for continuous small-to-medium batch production</li>
  <li>Threading, knurling and grooving capabilities</li>
</ul>
<h3>Quality Control</h3>
<ul>
  <li>First-article full CMM dimensional report</li>
  <li>Batch sampling plus 100% inspection of critical dimensions</li>
  <li>Material certificates and heat-treatment reports included</li>
</ul>
HTML,
                ],
            ],

            // ==================== CNC machining (catalog 2) ====================

            [
                'id'         => 5,
                'catalog_id' => 2,
                'slug'       => 'aluminum-cnc-parts',
                'image'      => 'images/demo/products/aluminum-cnc.jpg',
                'viewed'     => 312,
                'author'     => 'Apex Precision',
                'tags'       => [1, 5, 2],
                'zh'         => [
                    'title'   => '铝合金 CNC 加工件',
                    'summary' => '6061/7075 铝合金精密加工，切削效率高、成本可控，配套阳极氧化等表面工艺。',
                    'content' => <<<'HTML'
<p>铝合金是 CNC 加工中应用最广的材料，兼具轻量化、导热好、易加工与成本友好的优势。傲锋常备 6061-T6、6063、7075、5083 等牌号铝材，来料与自采均可。</p>
<h3>工艺要点</h3>
<ul>
  <li>专用铝用刀具与高速切削参数，镜面效果可达 Ra 0.4</li>
  <li>应力释放处理，防止薄壁件加工变形</li>
  <li>配套阳极氧化（本色/黑色/彩色）、喷砂、拉丝</li>
</ul>
<h3>典型产品</h3>
<ul>
  <li>无人机结构件、散热器、光学镜筒</li>
  <li>自动化夹具、半导体设备腔体</li>
  <li>消费电子金属外壳与按键</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Aluminum CNC Parts',
                    'summary' => 'Precision machining of 6061/7075 aluminum — high cutting efficiency, controlled cost, with in-house anodizing.',
                    'content' => <<<'HTML'
<p>Aluminum is the most widely machined material, combining light weight, thermal conductivity, machinability and cost efficiency. Apex stocks 6061-T6, 6063, 7075 and 5083 grades; customer-supplied material is welcome.</p>
<h3>Process Highlights</h3>
<ul>
  <li>Aluminum-specific tooling and HSM parameters, finishes to Ra 0.4</li>
  <li>Stress-relief treatment prevents thin-wall distortion</li>
  <li>In-house anodizing (clear/black/color), bead blasting, brushing</li>
</ul>
<h3>Typical Products</h3>
<ul>
  <li>Drone frames, heat sinks, optical lens barrels</li>
  <li>Automation fixtures, semiconductor chambers</li>
  <li>Consumer electronics housings and buttons</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 6,
                'catalog_id' => 2,
                'slug'       => 'brass-cnc-parts',
                'image'      => 'images/demo/products/brass-cnc.jpg',
                'viewed'     => 245,
                'author'     => 'Apex Precision',
                'tags'       => [1, 2],
                'zh'         => [
                    'title'   => '黄铜精密加工件',
                    'summary' => 'H59/H62/C3604 黄铜件加工，导电导热性能优异，适用于接头、阀门与电气端子。',
                    'content' => <<<'HTML'
<p>黄铜切削性能优异、表面光洁度高，并具备良好的导电导热与耐腐蚀性能，是流体接头、电气端子、阀门零件与装饰件的首选材料。</p>
<h3>服务范围</h3>
<ul>
  <li>H59、H62、C3604 易切削黄铜，铍铜可定制</li>
  <li>车削 + 铣削复合工艺，微小特征加工精度 ±0.01mm</li>
  <li>配套镀镍、镀铬、钝化处理</li>
</ul>
<h3>应用场景</h3>
<ul>
  <li>气动/液压接头、阀芯阀座</li>
  <li>电气端子、探针、导电柱</li>
  <li>卫浴五金、装饰配件</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Brass CNC Parts',
                    'summary' => 'Machining of H59/H62/C3604 brass with excellent conductivity — ideal for fittings, valves and electrical terminals.',
                    'content' => <<<'HTML'
<p>Brass machines beautifully with high surface quality, and offers excellent electrical/thermal conductivity and corrosion resistance — the first choice for fluid fittings, electrical terminals, valve parts and decorative hardware.</p>
<h3>Service Scope</h3>
<ul>
  <li>Free-machining brass H59, H62, C3604; beryllium copper on request</li>
  <li>Mill-turn processes hold ±0.01mm on fine features</li>
  <li>Nickel plating, chrome plating and passivation available</li>
</ul>
<h3>Applications</h3>
<ul>
  <li>Pneumatic/hydraulic fittings, valve cores and seats</li>
  <li>Electrical terminals, test probes, contact pins</li>
  <li>Sanitary hardware and decorative fittings</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 7,
                'catalog_id' => 2,
                'slug'       => 'titanium-cnc-parts',
                'image'      => 'images/demo/products/titanium-cnc.jpg',
                'viewed'     => 287,
                'author'     => 'Apex Precision',
                'tags'       => [1, 7],
                'zh'         => [
                    'title'   => '钛合金加工件',
                    'summary' => 'Gr2/Gr5 钛合金精密加工，攻克导热差、易粘刀难题，服务医疗植入与航空客户。',
                    'content' => <<<'HTML'
<p>钛合金比强度高、生物相容性好，但导热差、加工硬化严重，是公认的难加工材料。傲锋积累了十余年钛合金加工经验，为医疗器械与航空航天客户提供稳定交付。</p>
<h3>工艺方案</h3>
<ul>
  <li>低温高压冷却系统，抑制刀尖积屑瘤</li>
  <li>专用硬质合金刀具，小切深高进给策略</li>
  <li>Gr2、Gr5（Ti-6Al-4V）、Ti-6Al-7Nb 常备材料</li>
  <li>加工后去应力退火，尺寸长期稳定</li>
</ul>
<h3>典型产品</h3>
<ul>
  <li>骨科植入物试制件、手术器械</li>
  <li>航空紧固件、无人机结构件</li>
  <li>高端运动器材与钟表零件</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Titanium CNC Parts',
                    'summary' => 'Precision machining of Gr2/Gr5 titanium, solving poor thermal conductivity and work hardening for medical and aerospace clients.',
                    'content' => <<<'HTML'
<p>Titanium offers an outstanding strength-to-weight ratio and biocompatibility, yet its poor thermal conductivity and severe work hardening make it notoriously difficult to machine. With over a decade of titanium experience, Apex delivers stable quality for medical and aerospace clients.</p>
<h3>Process Solutions</h3>
<ul>
  <li>High-pressure coolant to suppress built-up edge</li>
  <li>Dedicated carbide tooling with low-DOC, high-feed strategies</li>
  <li>Gr2, Gr5 (Ti-6Al-4V) and Ti-6Al-7Nb stocked</li>
  <li>Post-machining stress relief for long-term stability</li>
</ul>
<h3>Typical Products</h3>
<ul>
  <li>Orthopedic implant prototypes, surgical instruments</li>
  <li>Aerospace fasteners, UAV structures</li>
  <li>Premium sporting goods and watch components</li>
</ul>
HTML,
                ],
            ],

            // ==================== Sheet metal (catalog 3) ====================

            [
                'id'         => 8,
                'catalog_id' => 3,
                'slug'       => 'sheet-metal-enclosures',
                'image'      => 'images/demo/products/sheet-enclosure.jpg',
                'viewed'     => 298,
                'author'     => 'Apex Precision',
                'tags'       => [3, 2],
                'zh'         => [
                    'title'   => '定制钣金机箱机柜',
                    'summary' => '激光切割、数控折弯、焊接铆接一体化，定制设备机箱、机柜与结构件，支持小批量快反。',
                    'content' => <<<'HTML'
<p>傲锋钣金事业部提供从激光下料、折弯成型到焊接装配的完整产线，为自动化设备、通讯、医疗与新能源客户定制机箱机柜及结构件。</p>
<h3>加工能力</h3>
<ul>
  <li>6kW 激光切割：碳钢 ≤ 25mm，不锈钢 ≤ 12mm，铝板 ≤ 10mm</li>
  <li>数控折弯 160T，最长 3200mm</li>
  <li>氩弧焊、气保焊、激光焊与种钉、铆接</li>
  <li>配套喷粉、喷塑、丝印一站式交付</li>
</ul>
<h3>服务优势</h3>
<ul>
  <li>DFM 可制造性评审，优化折弯与焊接工艺</li>
  <li>打样 5–10 天，小批量 15–20 天</li>
  <li>整机组装与老化测试支持</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Custom Sheet Metal Enclosures',
                    'summary' => 'Laser cutting, CNC bending and welding under one roof — custom equipment enclosures and frames with fast low-volume turnaround.',
                    'content' => <<<'HTML'
<p>Apex's sheet metal division runs a complete line from laser blanking and bending to welding and assembly, building enclosures and structural parts for automation, telecom, medical and new-energy customers.</p>
<h3>Capabilities</h3>
<ul>
  <li>6kW laser: carbon steel ≤ 25mm, stainless ≤ 12mm, aluminum ≤ 10mm</li>
  <li>CNC press brakes 160T, bed length up to 3200mm</li>
  <li>TIG, MIG, laser welding plus stud welding and riveting</li>
  <li>Powder coating, painting and silk-screening in-house</li>
</ul>
<h3>Service Advantages</h3>
<ul>
  <li>DFM review to optimize bending and welding</li>
  <li>Prototypes in 5–10 days, low-volume in 15–20 days</li>
  <li>Full assembly and burn-in testing supported</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 9,
                'catalog_id' => 3,
                'slug'       => 'stainless-steel-sheet-metal',
                'image'      => 'images/demo/products/stainless-sheet.jpg',
                'viewed'     => 189,
                'author'     => 'Apex Precision',
                'tags'       => [6, 2],
                'zh'         => [
                    'title'   => '不锈钢钣金件',
                    'summary' => '304/316L 不锈钢钣金加工，耐腐蚀、易清洁，适用于食品机械、医疗设备与户外设施。',
                    'content' => <<<'HTML'
<p>不锈钢钣金件在食品机械、医疗设备、化工与户外设施中不可替代。我们加工 304、316L、201 等牌号，兼顾结构强度与外观质量。</p>
<h3>工艺控制</h3>
<ul>
  <li>不锈钢专用切割断面，无挂渣、氧化层薄</li>
  <li>焊接采用背面充氩保护，焊缝均匀可抛光</li>
  <li>表面拉丝、8K 镜面、喷砂、钝化可选</li>
</ul>
<h3>典型产品</h3>
<ul>
  <li>食品机械外壳与料斗</li>
  <li>医疗推车、器械柜</li>
  <li>户外机柜、防雨箱体</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Stainless Steel Sheet Metal',
                    'summary' => '304/316L stainless fabrication — corrosion resistant and easy to clean for food machinery, medical equipment and outdoor cabinets.',
                    'content' => <<<'HTML'
<p>Stainless steel fabrication is irreplaceable in food machinery, medical equipment, chemical plants and outdoor infrastructure. We process 304, 316L and 201 grades with equal attention to structural strength and appearance.</p>
<h3>Process Control</h3>
<ul>
  <li>Clean laser-cut edges with minimal dross and oxide layer</li>
  <li>Back-purged TIG welding produces polishable beads</li>
  <li>Hairline, 8K mirror, bead blast and passivation finishes</li>
</ul>
<h3>Typical Products</h3>
<ul>
  <li>Food machinery housings and hoppers</li>
  <li>Medical carts and instrument cabinets</li>
  <li>Outdoor cabinets and weatherproof enclosures</li>
</ul>
HTML,
                ],
            ],

            // ==================== Die casting (catalog 4) ====================

            [
                'id'         => 10,
                'catalog_id' => 4,
                'slug'       => 'aluminum-die-casting-parts',
                'image'      => 'images/demo/products/aluminum-die-casting.jpg',
                'viewed'     => 267,
                'author'     => 'Apex Precision',
                'tags'       => [5, 3],
                'zh'         => [
                    'title'   => '铝合金压铸件',
                    'summary' => 'ADC12/A380 高压压铸，80–800T 机台覆盖中小件，配套 CNC 精加工与气密性测试。',
                    'content' => <<<'HTML'
<p>压铸适合大批量、形状复杂的铝合金结构件，单件节拍可达数十秒。傲锋配备 80–800T 冷室压铸机 12 台，年产压铸件超 500 万件。</p>
<h3>生产能力</h3>
<ul>
  <li>ADC12、A380、A360、AlSi10MnMg 等材料</li>
  <li>模流分析（Moldflow）前置评审，降低缩孔风险</li>
  <li>X-Ray 探伤 + CMM 全尺寸检测</li>
  <li>配套 CNC 二次加工、攻牙、去毛刺与喷粉</li>
</ul>
<h3>典型应用</h3>
<ul>
  <li>汽车发动机支架、变速箱壳体</li>
  <li>LED 灯具散热器、通讯滤波器腔体</li>
  <li>电动工具外壳、电机端盖</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Aluminum Die Casting Parts',
                    'summary' => 'ADC12/A380 high-pressure die casting on 80–800T machines, with CNC finishing and leak testing in-house.',
                    'content' => <<<'HTML'
<p>Die casting suits high-volume, geometrically complex aluminum structures with cycle times of just seconds. Apex runs twelve 80–800T cold-chamber machines, producing over 5 million castings per year.</p>
<h3>Production Capabilities</h3>
<ul>
  <li>ADC12, A380, A360, AlSi10MnMg alloys</li>
  <li>Upfront mold-flow analysis to minimize porosity</li>
  <li>X-ray inspection plus CMM dimensional checks</li>
  <li>Secondary CNC machining, tapping, deburring and powder coating</li>
</ul>
<h3>Typical Applications</h3>
<ul>
  <li>Engine brackets, transmission housings</li>
  <li>LED heat sinks, telecom filter cavities</li>
  <li>Power-tool housings, motor end caps</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 11,
                'catalog_id' => 4,
                'slug'       => 'zinc-alloy-die-casting',
                'image'      => 'images/demo/products/zinc-die-casting.jpg',
                'viewed'     => 154,
                'author'     => 'Apex Precision',
                'tags'       => [3],
                'zh'         => [
                    'title'   => '锌合金压铸件',
                    'summary' => '3# / 5# 锌合金热室压铸，精度高、表面质量好，适合锁具、汽配与装饰五金。',
                    'content' => <<<'HTML'
<p>锌合金熔点低、流动性好，可采用热室压铸实现高精度与优异表面质量，特别适合外观件与小型精密件。</p>
<h3>工艺特点</h3>
<ul>
  <li>热室压铸机 25–160T，节拍快、成本低</li>
  <li>尺寸精度可达 ±0.02mm，表面光洁度高</li>
  <li>配套抛光、电镀（镀锌/镍/铬）、喷涂</li>
</ul>
<h3>典型产品</h3>
<ul>
  <li>锁具锁体、拉手合页</li>
  <li>汽车标牌、雨刮配件</li>
  <li>箱包五金、卫浴挂件</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Zinc Alloy Die Casting',
                    'summary' => 'Hot-chamber zinc die casting (3#/5#) with tight accuracy and premium surface quality for locks, auto trim and hardware.',
                    'content' => <<<'HTML'
<p>Zinc alloys' low melting point and excellent fluidity enable hot-chamber die casting with tight accuracy and superb surface quality — ideal for cosmetic and small precision parts.</p>
<h3>Process Features</h3>
<ul>
  <li>25–160T hot-chamber machines, fast cycles, low cost</li>
  <li>Dimensional accuracy to ±0.02mm with fine surface finish</li>
  <li>Polishing and plating (zinc/nickel/chrome) in-house</li>
</ul>
<h3>Typical Products</h3>
<ul>
  <li>Lock bodies, handles and hinges</li>
  <li>Automotive badges, wiper components</li>
  <li>Luggage hardware, bathroom accessories</li>
</ul>
HTML,
                ],
            ],

            // ==================== Surface finishing (catalog 5) ====================

            [
                'id'         => 12,
                'catalog_id' => 5,
                'slug'       => 'anodizing-powder-coating',
                'image'      => 'images/demo/products/anodizing.jpg',
                'viewed'     => 221,
                'author'     => 'Apex Precision',
                'tags'       => [5],
                'zh'         => [
                    'title'   => '阳极氧化与喷粉服务',
                    'summary' => '自有阳极氧化线与喷粉线，膜厚可控、色差 ΔE ≤ 1.5，满足外观件与防腐要求。',
                    'content' => <<<'HTML'
<p>表面处理决定产品的第一印象。傲锋自有阳极氧化与静电喷粉产线，关键外观指标全程受控。</p>
<h3>阳极氧化</h3>
<ul>
  <li>普通氧化膜厚 10–25μm，硬质氧化可达 50μm</li>
  <li>本色、黑色及潘通色卡定制着色</li>
  <li>盐雾测试 ≥ 48h（按 ASTM B117）</li>
</ul>
<h3>静电喷粉</h3>
<ul>
  <li>环氧、聚酯、环氧聚酯混合粉末</li>
  <li>膜厚 60–120μm，百格测试 5B</li>
  <li>色差 ΔE ≤ 1.5，光泽度按样板定制</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Anodizing & Powder Coating',
                    'summary' => 'In-house anodizing and powder coating lines with controlled film thickness and color difference ΔE ≤ 1.5.',
                    'content' => <<<'HTML'
<p>Surface finishing defines the first impression of a product. Apex operates its own anodizing and electrostatic powder-coating lines with all key cosmetic metrics under control.</p>
<h3>Anodizing</h3>
<ul>
  <li>Standard film 10–25μm, hard anodizing up to 50μm</li>
  <li>Clear, black and custom Pantone colors</li>
  <li>Salt spray ≥ 48h per ASTM B117</li>
</ul>
<h3>Powder Coating</h3>
<ul>
  <li>Epoxy, polyester and hybrid powders</li>
  <li>Film 60–120μm, cross-cut adhesion 5B</li>
  <li>ΔE ≤ 1.5, gloss customized to samples</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 13,
                'catalog_id' => 5,
                'slug'       => 'polishing-electroplating',
                'image'      => 'images/demo/products/polishing.jpg',
                'viewed'     => 143,
                'author'     => 'Apex Precision',
                'tags'       => [6],
                'zh'         => [
                    'title'   => '抛光、电镀与钝化',
                    'summary' => '机械抛光/电解抛光、镀镍镀铬、不锈钢钝化，提升零件外观、耐磨与防腐性能。',
                    'content' => <<<'HTML'
<p>针对高外观与高防腐需求，傲锋提供完整的精整工艺组合。</p>
<h3>抛光</h3>
<ul>
  <li>机械抛光：镜面效果 Ra ≤ 0.05</li>
  <li>电解抛光：不锈钢件表面光洁、去毛刺彻底</li>
</ul>
<h3>电镀</h3>
<ul>
  <li>镀镍、镀铬、镀金、镀锡（外协认证产线）</li>
  <li>膜厚 X-Ray 检测，符合 RoHS/REACH</li>
</ul>
<h3>钝化</h3>
<ul>
  <li>不锈钢柠檬酸钝化，盐雾 ≥ 96h 无红锈</li>
  <li>铝件化学转化膜（铬化/无铬）</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Polishing, Plating & Passivation',
                    'summary' => 'Mechanical/electrolytic polishing, nickel and chrome plating, and passivation for superior looks, wear and corrosion resistance.',
                    'content' => <<<'HTML'
<p>For demanding cosmetic and corrosion requirements, Apex offers a complete set of finishing processes.</p>
<h3>Polishing</h3>
<ul>
  <li>Mechanical polishing to mirror finish, Ra ≤ 0.05</li>
  <li>Electropolishing for stainless parts — bright, fully deburred</li>
</ul>
<h3>Plating</h3>
<ul>
  <li>Nickel, chrome, gold and tin plating (certified partner lines)</li>
  <li>XRF thickness verification, RoHS/REACH compliant</li>
</ul>
<h3>Passivation</h3>
<ul>
  <li>Citric-acid passivation for stainless, ≥ 96h salt spray without red rust</li>
  <li>Chemical conversion coatings for aluminum (chromate / chrome-free)</li>
</ul>
HTML,
                ],
            ],

            // ==================== Industries (catalog 6 + children) ====================

            [
                'id'         => 14,
                'catalog_id' => 6,
                'slug'       => 'manufacturing-solutions-overview',
                'image'      => 'images/demo/industries/manufacturing.jpg',
                'viewed'     => 352,
                'author'     => 'Apex Precision',
                'tags'       => [3, 4],
                'zh'         => [
                    'title'   => '一站式精密制造解决方案',
                    'summary' => '从设计评审、打样验证到量产交付与售后支持，傲锋为各行业客户提供端到端的精密制造服务。',
                    'content' => <<<'HTML'
<p>不同行业对零部件的要求各有侧重：汽车看重过程能力与追溯，医疗强调洁净与合规，航空聚焦材料与认证，消费电子关注外观与成本。傲锋以统一的质量体系为底座，为各行业提供定制化制造方案。</p>
<h3>端到端服务流程</h3>
<ul>
  <li>DFM 设计评审：优化结构以降本增效</li>
  <li>快速打样：7–15 天交付首件并附全尺寸报告</li>
  <li>小批量验证：PPAP/FAI 文件齐套</li>
  <li>量产交付：安全库存与滚动预测管理</li>
  <li>售后支持：8D 报告 24 小时响应</li>
</ul>
<p>浏览左侧行业分类，了解我们在各领域的制造经验。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'One-Stop Precision Manufacturing Solutions',
                    'summary' => 'From design review and prototyping through mass production and after-sales support — end-to-end precision manufacturing for every industry.',
                    'content' => <<<'HTML'
<p>Every industry weights part requirements differently: automotive demands process capability and traceability, medical emphasizes cleanliness and compliance, aerospace focuses on materials and certification, and consumer electronics cares about cosmetics and cost. Apex builds on a unified quality system to deliver tailored manufacturing solutions.</p>
<h3>End-to-End Workflow</h3>
<ul>
  <li>DFM review: optimize designs to cut cost and risk</li>
  <li>Rapid prototyping: first articles in 7–15 days with full reports</li>
  <li>Low-volume validation: complete PPAP/FAI documentation</li>
  <li>Mass production: safety stock and rolling-forecast management</li>
  <li>After-sales: 8D response within 24 hours</li>
</ul>
<p>Browse the industry categories to explore our experience in each field.</p>
HTML,
                ],
            ],

            [
                'id'         => 15,
                'catalog_id' => 7,
                'slug'       => 'precision-automotive-components',
                'image'      => 'images/demo/industries/automotive.jpg',
                'viewed'     => 301,
                'author'     => 'Apex Precision',
                'tags'       => [4, 2],
                'zh'         => [
                    'title'   => '汽车精密零部件',
                    'summary' => '通过 IATF 16949 认证，为传统动力与新能源三电系统提供精密机加工与压铸零部件。',
                    'content' => <<<'HTML'
<p>傲锋通过 IATF 16949 汽车行业质量体系认证，量产件过程能力 CPK ≥ 1.67，全流程条码追溯。</p>
<h3>供应产品</h3>
<ul>
  <li>发动机与变速箱壳体、支架、法兰</li>
  <li>新能源电机壳体、电控散热基座、电池包结构件</li>
  <li>转向与制动系统精密轴类、阀体</li>
  <li>传感器外壳与连接器零件</li>
</ul>
<h3>过程控制</h3>
<ul>
  <li>APQP 全流程项目管理，PPAP Level 3 交付</li>
  <li>SPC 关键尺寸监控，MSA 量具分析</li>
  <li>三坐标、轮廓仪、粗糙度仪全尺寸检测</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Precision Automotive Components',
                    'summary' => 'IATF 16949 certified machining and die casting for conventional powertrains and new-energy EV systems.',
                    'content' => <<<'HTML'
<p>Apex is certified to IATF 16949, with production process capability of CPK ≥ 1.67 and full barcode traceability.</p>
<h3>Products Supplied</h3>
<ul>
  <li>Engine and transmission housings, brackets, flanges</li>
  <li>EV motor housings, inverter heat-sink bases, battery-pack structures</li>
  <li>Precision shafts and valve bodies for steering and braking</li>
  <li>Sensor housings and connector components</li>
</ul>
<h3>Process Control</h3>
<ul>
  <li>APQP program management, PPAP Level 3 submission</li>
  <li>SPC on critical dimensions, MSA gauge analysis</li>
  <li>CMM, profilometer and roughness inspection</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 16,
                'catalog_id' => 8,
                'slug'       => 'medical-device-components',
                'image'      => 'images/demo/industries/medical.jpg',
                'viewed'     => 276,
                'author'     => 'Apex Precision',
                'tags'       => [4, 2],
                'zh'         => [
                    'title'   => '医疗器械零部件',
                    'summary' => 'ISO 13485 体系管控，万级洁净车间，为诊断设备、手术器械与植入器械提供精密零件。',
                    'content' => <<<'HTML'
<p>医疗行业对零件的精度、洁净度与可追溯性要求严苛。傲锋按照 ISO 13485 建立医疗器械质量管理体系，配备万级洁净装配间。</p>
<h3>供应产品</h3>
<ul>
  <li>诊断与影像设备结构件、外壳</li>
  <li>手术器械零件：钳口、关节、手柄</li>
  <li>内窥镜零件、牙科手机零件</li>
  <li>骨科植入物试制件（钛合金）</li>
</ul>
<h3>合规保障</h3>
<ul>
  <li>材料生物相容性报告（ISO 10993 支持文件）</li>
  <li>批次全流程追溯，记录保存 ≥ 15 年</li>
  <li>超声波清洗 + 洁净包装</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Medical Device Components',
                    'summary' => 'ISO 13485 controlled manufacturing with cleanroom assembly for diagnostics, surgical instruments and implant prototypes.',
                    'content' => <<<'HTML'
<p>The medical industry imposes strict demands on precision, cleanliness and traceability. Apex operates an ISO 13485 quality system with Class 10,000 clean assembly rooms.</p>
<h3>Products Supplied</h3>
<ul>
  <li>Structural parts and housings for diagnostic and imaging equipment</li>
  <li>Surgical instrument parts: jaws, joints, handles</li>
  <li>Endoscope and dental handpiece components</li>
  <li>Titanium orthopedic implant prototypes</li>
</ul>
<h3>Compliance</h3>
<ul>
  <li>Biocompatibility documentation support (ISO 10993)</li>
  <li>Full batch traceability, records retained ≥ 15 years</li>
  <li>Ultrasonic cleaning and clean packaging</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 17,
                'catalog_id' => 9,
                'slug'       => 'aerospace-machined-parts',
                'image'      => 'images/demo/industries/aerospace.jpg',
                'viewed'     => 234,
                'author'     => 'Apex Precision',
                'tags'       => [7, 2],
                'zh'         => [
                    'title'   => '航空航天加工件',
                    'summary' => '钛合金、高温合金与高强度铝合金加工能力，满足航空航天对材料与精度的严苛要求。',
                    'content' => <<<'HTML'
<p>航空航天零件要求高强度重量比、耐高温与极端可靠性。傲锋为无人机、卫星部件与航空地面设备客户提供难加工材料解决方案。</p>
<h3>材料能力</h3>
<ul>
  <li>钛合金 Gr2/Gr5/Gr23</li>
  <li>高温合金 Inconel 718、Hastelloy</li>
  <li>高强铝 7075、2024、7050</li>
</ul>
<h3>质量保障</h3>
<ul>
  <li>五轴加工 + 在线测量，位置度 ≤ 0.02mm</li>
  <li>原材料光谱分析（PMI）逐批验证</li>
  <li>荧光渗透探伤（FPI）支持</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Aerospace Machined Parts',
                    'summary' => 'Titanium, superalloy and high-strength aluminum machining that meets aerospace demands for materials and precision.',
                    'content' => <<<'HTML'
<p>Aerospace parts demand high strength-to-weight ratios, temperature resistance and extreme reliability. Apex provides difficult-material solutions for UAV, satellite and aviation ground-equipment customers.</p>
<h3>Material Capabilities</h3>
<ul>
  <li>Titanium Gr2 / Gr5 / Gr23</li>
  <li>Superalloys: Inconel 718, Hastelloy</li>
  <li>High-strength aluminum: 7075, 2024, 7050</li>
</ul>
<h3>Quality Assurance</h3>
<ul>
  <li>5-axis machining with on-machine probing, true position ≤ 0.02mm</li>
  <li>Per-batch PMI spectrographic verification</li>
  <li>Fluorescent penetrant inspection (FPI) support</li>
</ul>
HTML,
                ],
            ],

            [
                'id'         => 18,
                'catalog_id' => 10,
                'slug'       => 'electronic-housings-components',
                'image'      => 'images/demo/industries/electronics.jpg',
                'viewed'     => 258,
                'author'     => 'Apex Precision',
                'tags'       => [5, 2],
                'zh'         => [
                    'title'   => '消费电子精密结构件',
                    'summary' => '兼顾外观与精度的消费电子金属件：外壳、散热件、屏蔽罩与连接器零件。',
                    'content' => <<<'HTML'
<p>消费电子产品对金属结构件的外观与精度同时敏感。傲锋为耳机、相机、智能穿戴与通讯设备品牌提供外观级精密零件。</p>
<h3>供应产品</h3>
<ul>
  <li>铝合金一体外壳（CNC + 阳极氧化）</li>
  <li>散热器、屏蔽罩、弹片</li>
  <li>Type-C 连接器零件、SIM 卡托</li>
  <li>智能穿戴中框与表壳</li>
</ul>
<h3>外观管控</h3>
<ul>
  <li>A 级面管控标准，限度样板封样管理</li>
  <li>色差 ΔE ≤ 1.0，膜厚在线监控</li>
  <li>全检 + AOI 外观筛选</li>
</ul>
HTML,
                ],
                'en' => [
                    'title'   => 'Consumer Electronics Components',
                    'summary' => 'Cosmetic-grade precision metal parts for electronics: housings, heat sinks, shields and connector components.',
                    'content' => <<<'HTML'
<p>Consumer electronics are simultaneously sensitive to cosmetics and precision. Apex supplies cosmetic-grade metal parts to earphone, camera, wearable and telecom device brands.</p>
<h3>Products Supplied</h3>
<ul>
  <li>Unibody aluminum housings (CNC + anodizing)</li>
  <li>Heat sinks, shielding cans, contact springs</li>
  <li>Type-C connector parts, SIM trays</li>
  <li>Wearable frames and watch cases</li>
</ul>
<h3>Cosmetic Control</h3>
<ul>
  <li>Class-A surface standards with limit-sample management</li>
  <li>ΔE ≤ 1.0 color control, in-line film-thickness monitoring</li>
  <li>100% inspection plus AOI cosmetic screening</li>
</ul>
HTML,
                ],
            ],

            // ==================== News (catalog 11 + children) ====================

            [
                'id'         => 19,
                'catalog_id' => 11,
                'slug'       => 'apex-expands-manufacturing-capacity',
                'image'      => 'images/demo/news/new-facility.jpg',
                'viewed'     => 412,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '傲锋精密二期工厂投产，产能提升 60%',
                    'summary' => '占地 6,000㎡ 的二期智造工厂正式投产，新增 20 台 CNC 加工中心与自动化产线，整体产能提升 60%。',
                    'content' => <<<'HTML'
<p>2026 年 3 月，傲锋精密二期智造工厂在东莞长安正式投产。新工厂占地 6,000㎡，引入 20 台高性能 CNC 加工中心、2 条柔性加工单元（FMS）及自动化上下料系统。</p>
<h3>关键升级</h3>
<ul>
  <li>整体机加工产能提升 60%</li>
  <li>夜间无人值守加工，设备利用率提升至 85%</li>
  <li>新增恒温检测室，三坐标数量翻倍</li>
</ul>
<p>总经理表示："二期工厂的投产将显著缩短客户的交付周期，也为承接医疗与航空领域的新项目奠定了硬件基础。"</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Apex Precision Opens Phase-II Plant, Boosting Capacity by 60%',
                    'summary' => 'Our new 6,000㎡ smart factory is now operational with 20 additional CNC centers and automation lines — total capacity up 60%.',
                    'content' => <<<'HTML'
<p>In March 2026, Apex Precision officially commissioned its Phase-II smart factory in Chang'an, Dongguan. The 6,000㎡ facility adds 20 high-performance CNC centers, two flexible manufacturing systems (FMS) and automated loading.</p>
<h3>Key Upgrades</h3>
<ul>
  <li>Overall machining capacity increased by 60%</li>
  <li>Lights-out night machining raises utilization to 85%</li>
  <li>New temperature-controlled metrology room doubles CMM capacity</li>
</ul>
<p>"The Phase-II plant shortens delivery cycles and lays the hardware foundation for new medical and aerospace programs," said the General Manager.</p>
HTML,
                ],
            ],

            [
                'id'         => 20,
                'catalog_id' => 11,
                'slug'       => 'precision-machining-trends-2026',
                'image'      => 'images/demo/news/industry-trend.jpg',
                'viewed'     => 388,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '2026 精密加工行业三大趋势',
                    'summary' => '五轴普及化、AI 辅助编程与近岸供应链重构，正在重塑精密加工行业的竞争格局。',
                    'content' => <<<'HTML'
<p>2026 年，精密加工行业正经历深刻变革。我们观察到三大趋势：</p>
<h3>一、五轴加工加速普及</h3>
<p>五轴设备价格持续下探，越来越多工厂将其作为标准配置。工序集中带来的交期优势，正在成为订单竞争的关键变量。</p>
<h3>二、AI 进入 CAM 编程</h3>
<p>AI 辅助刀路生成与加工参数优化开始规模化落地，编程效率提升 3–5 倍，新手工程师也能输出稳定工艺。</p>
<h3>三、供应链近岸化</h3>
<p>欧美客户在保留中国供应链的同时布局近岸备份，"中国 + 1"策略使具备快速响应与工程服务能力的工厂更受青睐。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Three Trends Reshaping Precision Machining in 2026',
                    'summary' => '5-axis adoption, AI-assisted CAM and nearshore supply chains are redrawing the competitive landscape of precision machining.',
                    'content' => <<<'HTML'
<p>The precision machining industry is undergoing profound change in 2026. We see three defining trends:</p>
<h3>1. 5-Axis Goes Mainstream</h3>
<p>Falling equipment prices make five-axis a standard configuration for more shops. The lead-time advantage of consolidated operations is becoming decisive in winning orders.</p>
<h3>2. AI Enters CAM</h3>
<p>AI-assisted toolpath generation and cutting-parameter optimization are scaling, boosting programming efficiency 3–5x and enabling junior engineers to deliver stable processes.</p>
<h3>3. Nearshoring Reshapes Supply Chains</h3>
<p>Western buyers keep China supply lines while building nearshore backups. The "China + 1" strategy favors factories with fast response and strong engineering services.</p>
HTML,
                ],
            ],

            [
                'id'         => 21,
                'catalog_id' => 12,
                'slug'       => 'iso-certification-renewal-2026',
                'image'      => 'images/demo/news/iso-certification.jpg',
                'viewed'     => 356,
                'author'     => 'Apex Precision',
                'tags'       => [4],
                'zh'         => [
                    'title'   => '傲锋精密顺利通过 ISO 9001:2015 年度监督审核',
                    'summary' => '审核组对过程控制与追溯体系给予高度评价，质量管理体系连续 8 年零不符合项通过。',
                    'content' => <<<'HTML'
<p>2026 年 5 月，第三方认证机构对傲锋精密进行了 ISO 9001:2015 年度监督审核。审核覆盖设计开发、采购、生产、检测与交付全流程，公司以零不符合项的成绩连续第 8 年通过审核。</p>
<p>审核组特别肯定了公司在关键尺寸 SPC 监控与批次追溯方面的实践，认为体系运行"有效且具有行业示范意义"。</p>
<p>目前，傲锋已建立 ISO 9001、IATF 16949、ISO 13485 三大体系融合的质量管理平台，为客户提供统一的高标准质量保障。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Apex Passes ISO 9001:2015 Surveillance Audit — 8th Consecutive Year',
                    'summary' => 'Auditors praised our process control and traceability; the QMS passed with zero nonconformities for the eighth straight year.',
                    'content' => <<<'HTML'
<p>In May 2026, a third-party certification body conducted the annual ISO 9001:2015 surveillance audit at Apex Precision. Covering design, purchasing, production, inspection and delivery, the company passed with zero nonconformities — the eighth consecutive year.</p>
<p>Auditors highlighted our SPC monitoring of critical dimensions and batch traceability, describing the system as "effective and exemplary for the industry."</p>
<p>Apex now runs an integrated quality platform spanning ISO 9001, IATF 16949 and ISO 13485, delivering a unified high standard of quality assurance.</p>
HTML,
                ],
            ],

            [
                'id'         => 22,
                'catalog_id' => 12,
                'slug'       => 'five-axis-machining-centers-commissioned',
                'image'      => 'images/demo/news/automation.jpg',
                'viewed'     => 298,
                'author'     => 'Apex Precision',
                'tags'       => [7],
                'zh'         => [
                    'title'   => '4 台五轴加工中心完成安装调试',
                    'summary' => '新引进的四台五轴联动加工中心完成验收，五轴设备总数达 12 台，复杂零件产能翻倍。',
                    'content' => <<<'HTML'
<p>6 月，傲锋新引进的 4 台五轴联动加工中心全部完成安装调试并通过验收，公司五轴设备总数达到 12 台。</p>
<h3>新增设备亮点</h3>
<ul>
  <li>定位精度 0.003mm，配备在线测头与刀具破损检测</li>
  <li>支持钛合金、高温合金重切削</li>
  <li>接入 MES 系统，加工数据实时采集</li>
</ul>
<p>设备负责人介绍，新产线将使复杂零件的平均交期从 15 天缩短至 10 天，并进一步支撑航空与医疗项目的产能需求。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Four New 5-Axis Machining Centers Commissioned',
                    'summary' => 'Our five-axis fleet reaches 12 machines after four new centers passed acceptance — complex-part capacity doubled.',
                    'content' => <<<'HTML'
<p>In June, Apex commissioned four new simultaneous 5-axis machining centers, bringing the total five-axis fleet to 12 machines.</p>
<h3>New Equipment Highlights</h3>
<ul>
  <li>0.003mm positioning accuracy with on-machine probing and tool-breakage detection</li>
  <li>Heavy cutting capability for titanium and superalloys</li>
  <li>MES-connected for real-time production data</li>
</ul>
<p>The new line shortens average lead times for complex parts from 15 to 10 days and further supports aerospace and medical program capacity, according to our equipment manager.</p>
HTML,
                ],
            ],

            [
                'id'         => 23,
                'catalog_id' => 12,
                'slug'       => 'apex-at-canton-fair-2026',
                'image'      => 'images/demo/news/trade-show.jpg',
                'viewed'     => 267,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '傲锋精密亮相 2026 春季广交会',
                    'summary' => '五天展期接待来自 32 个国家的 400 余位采购商，现场达成多项打样合作意向。',
                    'content' => <<<'HTML'
<p>4 月，傲锋精密携 CNC 加工、钣金与压铸三大产品系列亮相第 139 届广交会加工机械与零部件展区。</p>
<p>五天展期内，公司展台共接待来自 32 个国家的 400 余位专业采购商，现场收集图纸询价 120 余份，并与多家欧洲自动化设备制造商达成打样合作意向。</p>
<p>海外事业部负责人表示："广交会是展示中国精密制造能力的最佳窗口。我们将持续参加国内外专业展会，与全球客户面对面交流。"</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Apex Precision at the 2026 Spring Canton Fair',
                    'summary' => 'Over 400 buyers from 32 countries visited our booth; multiple prototyping partnerships were initiated on site.',
                    'content' => <<<'HTML'
<p>In April, Apex Precision showcased its CNC machining, sheet metal and die casting portfolios at the 139th Canton Fair.</p>
<p>During the five-day event, our booth hosted over 400 professional buyers from 32 countries, collected more than 120 drawing inquiries, and initiated prototyping partnerships with several European automation equipment manufacturers.</p>
<p>"The Canton Fair is the best window to showcase China's precision manufacturing capability. We will keep attending professional exhibitions worldwide," said the head of our overseas division.</p>
HTML,
                ],
            ],

            [
                'id'         => 24,
                'catalog_id' => 12,
                'slug'       => 'rd-center-officially-launched',
                'image'      => 'images/demo/news/rd-center.jpg',
                'viewed'     => 198,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '工程技术研发中心正式揭牌',
                    'summary' => '研发中心聚焦 DFM 工程服务、新材料工艺与自动化产线研发，工程师团队扩编至 35 人。',
                    'content' => <<<'HTML'
<p>2 月，傲锋精密工程技术研发中心正式揭牌。中心下设工艺工程、自动化与检测技术三个部门，工程师团队扩编至 35 人。</p>
<h3>重点方向</h3>
<ul>
  <li>客户图纸 DFM 优化与成本工程</li>
  <li>钛合金、复合材料等新材料加工工艺开发</li>
  <li>柔性加工单元与在线检测自动化</li>
</ul>
<p>研发中心的成立标志着傲锋从"来图加工"向"工程服务型制造"的战略升级。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Engineering R&D Center Officially Launched',
                    'summary' => 'The new center focuses on DFM services, new-material processes and automation; the engineering team grows to 35.',
                    'content' => <<<'HTML'
<p>In February, Apex Precision officially launched its Engineering R&D Center, organized into process engineering, automation and metrology divisions, with the engineering team expanded to 35 members.</p>
<h3>Key Focus Areas</h3>
<ul>
  <li>DFM optimization and cost engineering for customer drawings</li>
  <li>Process development for titanium, composites and new materials</li>
  <li>Flexible manufacturing cells and in-line inspection automation</li>
</ul>
<p>The center marks Apex's strategic upgrade from build-to-print machining to engineering-service manufacturing.</p>
HTML,
                ],
            ],

            [
                'id'         => 25,
                'catalog_id' => 13,
                'slug'       => 'cnc-automation-transforming-manufacturing',
                'image'      => 'images/demo/news/cnc-technology.jpg',
                'viewed'     => 245,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '自动化产线如何改变 CNC 加工成本结构',
                    'summary' => '机器人上下料与柔性制造系统让夜间无人加工成为常态，单件人工成本下降 40% 以上。',
                    'content' => <<<'HTML'
<p>随着机器人上下料与柔性制造系统（FMS）的成熟，CNC 加工的夜班无人值守已从概念走向常态。</p>
<h3>成本结构的变化</h3>
<ul>
  <li>设备利用率从 55% 提升至 85% 以上</li>
  <li>单件分摊人工成本下降 40%–60%</li>
  <li>质量波动收窄：夜间加工一致性反而更高</li>
</ul>
<p>业内专家认为，未来三年，自动化能力将成为中小加工厂能否进入头部客户供应链的分水岭。具备自动化产线与在线检测能力的工厂，将在报价竞争中占据结构性优势。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'How Automation Is Changing CNC Machining Economics',
                    'summary' => 'Robotic loading and FMS make lights-out machining routine, cutting per-part labor cost by over 40%.',
                    'content' => <<<'HTML'
<p>As robotic loading and flexible manufacturing systems (FMS) mature, lights-out CNC machining has moved from concept to routine.</p>
<h3>Shifting Cost Structures</h3>
<ul>
  <li>Equipment utilization rises from 55% to over 85%</li>
  <li>Per-part labor cost falls 40%–60%</li>
  <li>Variation narrows — overnight consistency often improves</li>
</ul>
<p>Industry experts expect automation capability to become the dividing line for entering top-tier supply chains within three years. Shops with automated lines and in-line inspection will hold a structural advantage in quoting.</p>
HTML,
                ],
            ],

            [
                'id'         => 26,
                'catalog_id' => 13,
                'slug'       => 'local-sourcing-reshapes-supply-chain',
                'image'      => 'images/demo/news/supply-chain.jpg',
                'viewed'     => 187,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '近岸采购兴起，中国精密制造如何应对',
                    'summary' => '"中国 + 1"策略下，响应速度、工程服务与质量体系成为留住海外订单的核心竞争力。',
                    'content' => <<<'HTML'
<p>近年来，欧美采购方在保留中国供应链的同时加速近岸布局，"中国 + 1"成为主流采购策略。这给中国精密制造企业带来了新的课题。</p>
<h3>留住订单的三个关键</h3>
<ul>
  <li><strong>速度</strong>：24 小时报价、7 天打样成为入场门槛</li>
  <li><strong>工程服务</strong>：DFM 优化能力帮助客户降本，形成粘性</li>
  <li><strong>体系信任</strong>：IATF/AS/ISO 认证与可追溯数据是基础</li>
</ul>
<p>分析师指出，单纯依靠价格优势的时代已经过去。能够提供"制造 + 工程服务"综合价值的工厂，仍将在全球供应链中保持不可替代的位置。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Nearshoring Rises: How Chinese Precision Manufacturing Responds',
                    'summary' => 'Under "China + 1", response speed, engineering services and quality systems are the keys to retaining overseas orders.',
                    'content' => <<<'HTML'
<p>Western buyers are accelerating nearshore layouts while keeping Chinese supply lines — "China + 1" has become the mainstream sourcing strategy, posing new questions for Chinese precision manufacturers.</p>
<h3>Three Keys to Retaining Orders</h3>
<ul>
  <li><strong>Speed</strong>: 24-hour quoting and 7-day prototyping are table stakes</li>
  <li><strong>Engineering</strong>: DFM optimization cuts customer costs and builds loyalty</li>
  <li><strong>Trust</strong>: IATF/AS/ISO certifications and traceable data are foundational</li>
</ul>
<p>Analysts note the era of competing on price alone is over. Shops delivering combined "manufacturing + engineering service" value will remain indispensable in global supply chains.</p>
HTML,
                ],
            ],

            [
                'id'         => 27,
                'catalog_id' => 13,
                'slug'       => 'china-precision-parts-export-growth',
                'image'      => 'images/demo/news/export-growth.jpg',
                'viewed'     => 212,
                'author'     => 'Apex Precision',
                'tags'       => [],
                'zh'         => [
                    'title'   => '海关数据：精密零部件出口连续三个季度增长',
                    'summary' => '高端装备与新能源需求拉动下，CNC 加工件与压铸件出口额同比增长 12.6%。',
                    'content' => <<<'HTML'
<p>最新海关统计显示，我国精密机械零部件出口额连续三个季度保持增长，最近一季度同比增长 12.6%，其中 CNC 加工件与铝压铸件贡献最大增量。</p>
<h3>增长驱动因素</h3>
<ul>
  <li>全球高端装备制造业复苏，设备零部件外包需求增加</li>
  <li>新能源汽车产业链出海，带动三电系统零件出口</li>
  <li>国内工厂自动化与质量体系升级，国际客户信任度提升</li>
</ul>
<p>行业商会提醒，出口企业应重视合规与知识产权保护，在报价阶段明确图纸与技术资料的保密条款，防范贸易风险。</p>
HTML,
                ],
                'en' => [
                    'title'   => 'Customs Data: Precision Parts Exports Grow for Third Straight Quarter',
                    'summary' => 'Driven by high-end equipment and new energy demand, CNC and die-casting exports rose 12.6% year over year.',
                    'content' => <<<'HTML'
<p>The latest customs statistics show China's precision mechanical parts exports grew for the third consecutive quarter, up 12.6% year over year, with CNC machined parts and aluminum die castings contributing the largest increments.</p>
<h3>Growth Drivers</h3>
<ul>
  <li>Global recovery in high-end equipment manufacturing increases outsourcing demand</li>
  <li>EV supply chains going overseas drive motor, inverter and battery part exports</li>
  <li>Automation and quality-system upgrades raise international buyer confidence</li>
</ul>
<p>The industry chamber reminds exporters to prioritize compliance and IP protection, clarifying NDA terms on drawings and technical documents at the quoting stage.</p>
HTML,
                ],
            ],
        ];
    }
}
