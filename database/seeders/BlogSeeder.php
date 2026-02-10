<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@rv.com.sa')->first();

        if (! $admin) {
            $admin = User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@rv.com.sa',
                'is_admin' => true,
            ]);
        }

        $categories = $this->createCategories();
        $tags = $this->createTags();
        $this->createPosts($admin, $categories, $tags);
    }

    /**
     * @return array<string, Category>
     */
    private function createCategories(): array
    {
        $items = [
            ['name_en' => 'Startup Insights', 'name_ar' => 'رؤى الشركات الناشئة', 'description_en' => 'Lessons learned from building and scaling startups.', 'description_ar' => 'دروس مستفادة من بناء وتوسيع الشركات الناشئة.'],
            ['name_en' => 'PropTech', 'name_ar' => 'تقنية العقارات', 'description_en' => 'Technology transforming the real estate industry.', 'description_ar' => 'التكنولوجيا التي تحول قطاع العقارات.'],
            ['name_en' => 'Venture Building', 'name_ar' => 'بناء المشاريع', 'description_en' => 'From idea to execution — how we build ventures.', 'description_ar' => 'من الفكرة إلى التنفيذ — كيف نبني المشاريع.'],
            ['name_en' => 'Saudi Vision 2030', 'name_ar' => 'رؤية السعودية 2030', 'description_en' => 'Opportunities emerging from Saudi Arabia\'s transformation.', 'description_ar' => 'الفرص الناشئة من تحول المملكة العربية السعودية.'],
            ['name_en' => 'Founder Stories', 'name_ar' => 'قصص المؤسسين', 'description_en' => 'Real stories from founders in our portfolio.', 'description_ar' => 'قصص حقيقية من المؤسسين في محفظتنا.'],
        ];

        $categories = [];
        foreach ($items as $item) {
            $categories[$item['name_en']] = Category::create([
                ...$item,
                'slug' => Str::slug($item['name_en']),
            ]);
        }

        return $categories;
    }

    /**
     * @return array<string, Tag>
     */
    private function createTags(): array
    {
        $items = [
            ['name_en' => 'Fundraising', 'name_ar' => 'جمع التمويل'],
            ['name_en' => 'Product Market Fit', 'name_ar' => 'ملاءمة المنتج للسوق'],
            ['name_en' => 'AI & Machine Learning', 'name_ar' => 'الذكاء الاصطناعي والتعلم الآلي'],
            ['name_en' => 'Smart Cities', 'name_ar' => 'المدن الذكية'],
            ['name_en' => 'Growth Strategy', 'name_ar' => 'استراتيجية النمو'],
            ['name_en' => 'Team Building', 'name_ar' => 'بناء الفريق'],
            ['name_en' => 'MENA Region', 'name_ar' => 'منطقة الشرق الأوسط'],
            ['name_en' => 'Sustainability', 'name_ar' => 'الاستدامة'],
        ];

        $tags = [];
        foreach ($items as $item) {
            $tags[$item['name_en']] = Tag::create([
                ...$item,
                'slug' => Str::slug($item['name_en']),
            ]);
        }

        return $tags;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<string, Tag>  $tags
     */
    private function createPosts(User $admin, array $categories, array $tags): void
    {
        $posts = [
            [
                'title_en' => 'Why Execution Beats Ideas in the Startup World',
                'title_ar' => 'لماذا يتفوق التنفيذ على الأفكار في عالم الشركات الناشئة',
                'excerpt_en' => 'Everyone has ideas. The founders who win are the ones who ship fast, learn faster, and never stop iterating. Here\'s what separates dreamers from builders.',
                'excerpt_ar' => 'الجميع لديه أفكار. المؤسسون الذين ينجحون هم من يطلقون بسرعة، ويتعلمون أسرع، ولا يتوقفون عن التطوير. إليك ما يفصل الحالمين عن البناة.',
                'content_en' => '<h2>The Myth of the Perfect Idea</h2><p>Walk into any startup event and you\'ll hear someone whisper about their "billion-dollar idea." They guard it like a state secret, afraid someone might steal it. But here\'s the truth most experienced founders already know: ideas are cheap. Execution is everything.</p><p>At Reality Venture, we\'ve seen hundreds of pitch decks. The ones that get funded aren\'t always the most original — they\'re the ones with founders who\'ve already started building, already talked to customers, and already have something to show.</p><h2>Speed as a Competitive Advantage</h2><p>In the early stages, your biggest competitor isn\'t another startup. It\'s time. Every week you spend perfecting your pitch deck instead of shipping is a week your potential customers are solving their problem some other way — or getting used to living with it.</p><p>The best founding teams we work with share one trait: they bias toward action. They\'d rather launch something imperfect and learn from real feedback than spend months building in isolation.</p><h2>What This Looks Like in Practice</h2><p>Here\'s what execution-first founders do differently:</p><ul><li><strong>They talk to users before writing code.</strong> Understanding the problem deeply saves months of wasted engineering time.</li><li><strong>They set weekly shipping goals.</strong> Not monthly. Not quarterly. Weekly. Momentum compounds.</li><li><strong>They measure what matters.</strong> Vanity metrics feel good. Revenue, retention, and engagement actually move the needle.</li><li><strong>They kill features that don\'t work.</strong> Sunk cost bias kills more startups than bad markets do.</li></ul><h2>The Bottom Line</h2><p>If you\'re sitting on an idea waiting for the "right time" to start — the right time was yesterday. Start small, ship fast, and let the market teach you what to build next. That\'s how real ventures are made.</p>',
                'content_ar' => '<h2>أسطورة الفكرة المثالية</h2><p>ادخل أي فعالية للشركات الناشئة وستسمع شخصًا يهمس عن "فكرته التي تساوي مليار دولار". يحرسها كسر من أسرار الدولة، خائفًا من أن يسرقها أحد. لكن هذه هي الحقيقة التي يعرفها معظم المؤسسين ذوي الخبرة: الأفكار رخيصة. التنفيذ هو كل شيء.</p><p>في رياليتي فينتشر، رأينا مئات العروض التقديمية. تلك التي تحصل على التمويل ليست دائمًا الأكثر أصالة — إنها تلك التي لديها مؤسسون بدأوا فعلاً في البناء، وتحدثوا مع العملاء، ولديهم ما يعرضونه.</p><h2>السرعة كميزة تنافسية</h2><p>في المراحل المبكرة، أكبر منافس لك ليس شركة ناشئة أخرى. إنه الوقت. كل أسبوع تقضيه في تحسين عرضك التقديمي بدلاً من إطلاق المنتج هو أسبوع يحل فيه عملاؤك المحتملون مشكلتهم بطريقة أخرى.</p><h2>الخلاصة</h2><p>إذا كنت تجلس على فكرة في انتظار "الوقت المناسب" للبدء — الوقت المناسب كان بالأمس. ابدأ صغيرًا، وأطلق بسرعة، ودع السوق يعلمك ما تبنيه بعد ذلك.</p>',
                'category' => 'Startup Insights',
                'tags' => ['Growth Strategy', 'Product Market Fit'],
                'published_at' => now()->subDays(2),
            ],
            [
                'title_en' => 'How PropTech Is Reshaping Real Estate in Saudi Arabia',
                'title_ar' => 'كيف تعيد تقنية العقارات تشكيل القطاع العقاري في السعودية',
                'excerpt_en' => 'From AI-powered property valuations to blockchain-based ownership records, Saudi Arabia\'s real estate sector is undergoing a digital revolution. Here\'s what\'s driving the change.',
                'excerpt_ar' => 'من تقييمات العقارات المدعومة بالذكاء الاصطناعي إلى سجلات الملكية القائمة على البلوكتشين، يشهد القطاع العقاري السعودي ثورة رقمية. إليك ما يقود هذا التغيير.',
                'content_en' => '<h2>A Market Ready for Disruption</h2><p>Saudi Arabia\'s real estate market is valued at over $300 billion, yet much of it still operates on handshake deals, paper contracts, and outdated listing platforms. That gap between market size and technological maturity represents one of the largest opportunities in the MENA region.</p><p>Vision 2030\'s push toward homeownership — targeting 70% by 2030, up from 47% in 2016 — is creating massive demand for modern, efficient real estate solutions. And that\'s exactly where PropTech comes in.</p><h2>Key Trends We\'re Watching</h2><p><strong>Digital Marketplaces:</strong> Platforms that aggregate listings, provide transparent pricing data, and streamline the search process are gaining rapid adoption. The days of calling five different brokers for the same apartment are numbered.</p><p><strong>Smart Building Management:</strong> IoT-enabled buildings that optimize energy consumption, predict maintenance needs, and enhance tenant experience are becoming the standard for new developments in NEOM and Riyadh.</p><p><strong>Fractional Ownership:</strong> Technology-enabled platforms allowing investors to own fractions of premium properties are democratizing access to real estate investment — a market previously reserved for high-net-worth individuals.</p><h2>Where Reality Venture Fits In</h2><p>Our PropTech vertical focuses on backing founders who understand both the technology and the local market dynamics. Saudi real estate isn\'t Silicon Valley — regulatory frameworks, cultural preferences, and infrastructure realities all shape what works here.</p><p>We\'re actively looking for founders building in property management automation, construction tech, and tenant experience platforms. If that\'s you, we should talk.</p>',
                'content_ar' => '<h2>سوق جاهز للتحول</h2><p>تُقدر قيمة سوق العقارات السعودي بأكثر من 300 مليار دولار، ومع ذلك لا يزال الكثير منه يعمل بصفقات تقليدية وعقود ورقية ومنصات قوائم قديمة. هذه الفجوة بين حجم السوق والنضج التكنولوجي تمثل واحدة من أكبر الفرص في منطقة الشرق الأوسط.</p><p>دفع رؤية 2030 نحو تملك المنازل — بهدف 70% بحلول 2030، ارتفاعًا من 47% في 2016 — يخلق طلبًا هائلاً على حلول عقارية حديثة وفعالة.</p><h2>اتجاهات رئيسية نتابعها</h2><p><strong>الأسواق الرقمية:</strong> المنصات التي تجمع القوائم وتوفر بيانات تسعير شفافة تكتسب اعتمادًا سريعًا.</p><p><strong>إدارة المباني الذكية:</strong> المباني المزودة بإنترنت الأشياء التي تحسن استهلاك الطاقة وتتنبأ باحتياجات الصيانة أصبحت المعيار للمشاريع الجديدة.</p><h2>أين تقف رياليتي فينتشر</h2><p>يركز قطاع تقنية العقارات لدينا على دعم المؤسسين الذين يفهمون التكنولوجيا وديناميكيات السوق المحلي.</p>',
                'category' => 'PropTech',
                'tags' => ['Smart Cities', 'AI & Machine Learning', 'MENA Region'],
                'published_at' => now()->subDays(5),
            ],
            [
                'title_en' => 'The Venture Building Playbook: From Zero to Market in 90 Days',
                'title_ar' => 'دليل بناء المشاريع: من الصفر إلى السوق في 90 يومًا',
                'excerpt_en' => 'We\'ve built over a dozen ventures from scratch. Here\'s the exact framework we use to go from a blank canvas to a live product with paying customers in just three months.',
                'excerpt_ar' => 'لقد بنينا أكثر من اثنتي عشرة مشروعًا من الصفر. إليك الإطار الدقيق الذي نستخدمه للانتقال من لوحة فارغة إلى منتج حي بعملاء يدفعون في ثلاثة أشهر فقط.',
                'content_en' => '<h2>Why 90 Days?</h2><p>Three months is long enough to validate a real business and short enough to maintain urgency. It forces brutal prioritization — you can\'t build everything, so you build the right things.</p><h2>Month 1: Discovery & Validation</h2><p>The first 30 days are about falling in love with the problem, not your solution. We conduct 30+ customer interviews, map the competitive landscape, and identify the one metric that matters most.</p><p>By the end of month one, you should be able to answer three questions clearly: Who is your customer? What specific pain are you solving? Why will they pay for it?</p><h2>Month 2: Build & Test</h2><p>With validated assumptions in hand, month two is about building the minimum lovable product. Not minimum viable — minimum lovable. There\'s a difference. Your first users should feel like you built it just for them.</p><p>We run weekly user testing sessions throughout this phase. Every Friday, real users interact with the latest build, and by Monday, the team has a prioritized list of what to fix.</p><h2>Month 3: Launch & Learn</h2><p>Month three is go-to-market. We launch publicly, activate our growth channels, and obsess over early traction signals. The goal isn\'t perfection — it\'s proof. Proof that real people want what you\'ve built and are willing to pay for it.</p><h2>What Happens After Day 90</h2><p>The 90-day sprint isn\'t the end — it\'s the beginning. If the data supports it, we double down with follow-on capital and operational support. If it doesn\'t, we pivot or wind down quickly. No ego, no sunk cost thinking. Just signal.</p>',
                'content_ar' => '<h2>لماذا 90 يومًا؟</h2><p>ثلاثة أشهر كافية للتحقق من مشروع حقيقي وقصيرة بما يكفي للحفاظ على الإلحاح. إنها تفرض أولويات صارمة — لا يمكنك بناء كل شيء، لذا تبني الأشياء الصحيحة.</p><h2>الشهر الأول: الاكتشاف والتحقق</h2><p>الأيام الثلاثين الأولى تدور حول الوقوع في حب المشكلة، وليس الحل. نجري أكثر من 30 مقابلة مع العملاء، ونرسم خريطة المنافسة، ونحدد المقياس الأهم.</p><h2>الشهر الثاني: البناء والاختبار</h2><p>مع افتراضات تم التحقق منها، الشهر الثاني يدور حول بناء المنتج الأدنى المحبوب. ليس الأدنى القابل للتطبيق — الأدنى المحبوب. هناك فرق.</p><h2>الشهر الثالث: الإطلاق والتعلم</h2><p>الشهر الثالث هو الذهاب إلى السوق. نطلق علنيًا، وننشط قنوات النمو، ونركز على إشارات الجذب المبكرة.</p>',
                'category' => 'Venture Building',
                'tags' => ['Growth Strategy', 'Product Market Fit', 'Team Building'],
                'published_at' => now()->subDays(8),
            ],
            [
                'title_en' => 'Saudi Arabia\'s Startup Ecosystem: What Global Investors Need to Know',
                'title_ar' => 'منظومة الشركات الناشئة في السعودية: ما يحتاج المستثمرون العالميون لمعرفته',
                'excerpt_en' => 'With $1.3 billion in VC funding in 2025 and a government fully committed to economic diversification, Saudi Arabia is no longer an emerging market — it\'s an arriving one.',
                'excerpt_ar' => 'مع 1.3 مليار دولار في تمويل رأس المال الجريء في 2025 وحكومة ملتزمة بالكامل بتنويع الاقتصاد، لم تعد السعودية سوقًا ناشئة — إنها سوق واصلة.',
                'content_en' => '<h2>The Numbers Tell the Story</h2><p>Saudi Arabia\'s startup ecosystem has grown tenfold in the last five years. VC funding crossed $1.3 billion in 2025. The number of active startups has tripled. And the government\'s Monsha\'at program has supported over 600,000 SMEs.</p><p>But the numbers only tell half the story. What\'s really driving the momentum is a fundamental shift in how Saudi Arabia sees itself — from an oil-dependent economy to a diversified, innovation-driven one.</p><h2>Why Now?</h2><p>Several factors are converging to make this the right time for startup investment in Saudi Arabia:</p><ul><li><strong>Young population:</strong> 60% of Saudi citizens are under 35, creating a massive consumer base that\'s digital-native and hungry for new products.</li><li><strong>Government backing:</strong> Vision 2030 isn\'t just a slogan. It\'s backed by hundreds of billions in sovereign wealth fund capital actively seeking private sector partnerships.</li><li><strong>Infrastructure investment:</strong> From NEOM to Riyadh Season to the Red Sea project, physical and digital infrastructure is being built at unprecedented scale.</li></ul><h2>Sectors to Watch</h2><p>FinTech, EdTech, HealthTech, and PropTech are the four verticals attracting the most attention. But we\'re also seeing early momentum in sustainability tech, logistics, and enterprise SaaS.</p><h2>How to Enter the Market</h2><p>For global investors, the key is finding the right local partners. Regulatory frameworks are evolving, cultural context matters, and on-the-ground relationships still drive deal flow. At Reality Venture, we bridge that gap.</p>',
                'content_ar' => '<h2>الأرقام تحكي القصة</h2><p>نمت منظومة الشركات الناشئة في السعودية عشرة أضعاف في السنوات الخمس الماضية. تجاوز تمويل رأس المال الجريء 1.3 مليار دولار في 2025.</p><p>لكن الأرقام تحكي نصف القصة فقط. ما يقود الزخم حقًا هو تحول جوهري في كيفية رؤية السعودية لنفسها.</p><h2>لماذا الآن؟</h2><p>عدة عوامل تتقاطع لتجعل هذا الوقت المناسب للاستثمار في الشركات الناشئة السعودية: سكان شباب، دعم حكومي، واستثمار في البنية التحتية.</p><h2>القطاعات الواعدة</h2><p>التقنية المالية، تقنية التعليم، التقنية الصحية، وتقنية العقارات هي القطاعات الأربعة الأكثر جذبًا للاهتمام.</p>',
                'category' => 'Saudi Vision 2030',
                'tags' => ['MENA Region', 'Fundraising', 'Growth Strategy'],
                'published_at' => now()->subDays(12),
            ],
            [
                'title_en' => 'From Corporate Job to Startup Founder: Lessons from Our Accelerator Alumni',
                'title_ar' => 'من الوظيفة إلى ريادة الأعمال: دروس من خريجي مسرعتنا',
                'excerpt_en' => 'Three founders from our latest accelerator cohort share what they wish they knew before making the leap — and what they\'d do exactly the same way again.',
                'excerpt_ar' => 'ثلاثة مؤسسين من آخر دفعة في مسرعتنا يشاركون ما تمنوا لو عرفوه قبل اتخاذ القرار — وما الذي سيفعلونه بنفس الطريقة مرة أخرى.',
                'content_en' => '<h2>The Leap Isn\'t as Scary as You Think</h2><p>Every founder in our accelerator program has a moment where they question everything. Was leaving a stable salary worth it? Am I actually cut out for this? What if it all fails?</p><p>We sat down with three alumni from our most recent cohort to get honest answers about the transition from corporate life to startup founder.</p><h2>Lesson 1: Your Corporate Skills Transfer — But Not the Ones You Expect</h2><p>"I thought my finance background would be my biggest asset," says Sara, who left a Big Four consulting firm to build a FinTech startup. "It helped, but what really mattered was my ability to navigate ambiguity. In consulting, you deal with incomplete information all the time. Startup life is that, but with higher stakes."</p><h2>Lesson 2: Build Before You\'re Ready</h2><p>Khalid spent two years researching his market before joining our accelerator. His biggest regret? "I should have started building on day one. The research was valuable, but I used it as a crutch to avoid the scary part — actually putting something in front of customers."</p><p>Within 60 days of joining our program, Khalid had his first paying customer. The product was rough, but it solved a real problem.</p><h2>Lesson 3: Your Network Is Your Net Worth</h2><p>"The single most valuable thing about the accelerator wasn\'t the funding," says Nora, who\'s building an EdTech platform. "It was being surrounded by people going through the same thing. On the hard days — and there are a lot of hard days — having founders who understand what you\'re dealing with is everything."</p><h2>Would They Do It Again?</h2><p>All three said yes without hesitation. Not because it\'s easy, but because the alternative — wondering "what if" — is worse.</p>',
                'content_ar' => '<h2>القفزة ليست مخيفة كما تعتقد</h2><p>كل مؤسس في برنامج مسرعتنا لديه لحظة يشكك فيها بكل شيء. هل كان ترك الراتب المستقر يستحق؟ هل أنا فعلاً مؤهل لهذا؟</p><p>جلسنا مع ثلاثة خريجين من أحدث دفعة لدينا للحصول على إجابات صادقة حول الانتقال من الحياة المؤسسية إلى ريادة الأعمال.</p><h2>الدرس الأول: مهاراتك المؤسسية تنتقل — لكن ليست تلك التي تتوقعها</h2><p>"اعتقدت أن خلفيتي في التمويل ستكون أكبر أصولي. لكن ما كان مهمًا حقًا هو قدرتي على التعامل مع الغموض."</p><h2>الدرس الثاني: ابنِ قبل أن تكون جاهزًا</h2><p>قضى خالد عامين في البحث في سوقه قبل الانضمام لمسرعتنا. أكبر ندمه؟ "كان يجب أن أبدأ في البناء من اليوم الأول."</p><h2>هل سيفعلونها مرة أخرى؟</h2><p>الثلاثة قالوا نعم بدون تردد. ليس لأنها سهلة، ولكن لأن البديل — التساؤل "ماذا لو" — أسوأ.</p>',
                'category' => 'Founder Stories',
                'tags' => ['Team Building', 'Growth Strategy', 'MENA Region'],
                'published_at' => now()->subDays(15),
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create([
                'user_id' => $admin->id,
                'category_id' => $categories[$postData['category']]->id,
                'title_en' => $postData['title_en'],
                'title_ar' => $postData['title_ar'],
                'slug' => Str::slug($postData['title_en']),
                'excerpt_en' => $postData['excerpt_en'],
                'excerpt_ar' => $postData['excerpt_ar'],
                'content_en' => $postData['content_en'],
                'content_ar' => $postData['content_ar'],
                'status' => PostStatus::Published,
                'published_at' => $postData['published_at'],
            ]);

            $tagIds = array_map(fn ($name) => $tags[$name]->id, $postData['tags']);
            $post->tags()->attach($tagIds);
        }
    }
}
