import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Lock, CheckCircle2, ArrowLeft, Send } from "lucide-react";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import i18n from "i18next";
import { Input } from "./ui/Input";
import { Select } from "./ui/Select";
import { MultiSelect } from "./ui/MultiSelect";
import { Checkbox } from "./ui/Checkbox";
import { Button } from "./ui/Button";
import { sectionVariants } from "./animations/CommonAnimations";

interface RvClubGateProps {
    postSlug: string;
}

const INTEREST_OPTIONS = [
    { value: "startups", labelKey: "common:newsletter.interests.options.startups" },
    { value: "proptech", labelKey: "common:newsletter.interests.options.proptech" },
    { value: "investment", labelKey: "common:newsletter.interests.options.investment" },
    { value: "venture_building", labelKey: "common:newsletter.interests.options.ventureBuilding" },
    { value: "technology", labelKey: "common:newsletter.interests.options.technology" },
    { value: "real_estate", labelKey: "common:newsletter.interests.options.realEstate" },
    { value: "entrepreneurship", labelKey: "common:newsletter.interests.options.entrepreneurship" },
    { value: "innovation", labelKey: "common:newsletter.interests.options.innovation" },
    { value: "games", labelKey: "common:newsletter.interests.options.games" },
    { value: "sport", labelKey: "common:newsletter.interests.options.sport" },
    { value: "hospitality", labelKey: "common:newsletter.interests.options.hospitality" },
    { value: "foodAndBeverage", labelKey: "common:newsletter.interests.options.foodAndBeverage" },
    { value: "healthcare", labelKey: "common:newsletter.interests.options.healthcare" },
    { value: "aiAndTech", labelKey: "common:newsletter.interests.options.aiAndTech" },
    { value: "manufacturing", labelKey: "common:newsletter.interests.options.manufacturing" },
];

export const RvClubGate: React.FC<RvClubGateProps> = ({
    postSlug,
}) => {
    const { t } = useTranslation(["blog", "common", "navigation"]);
    const isRtl = i18n.language === "ar";
    const [step, setStep] = useState<1 | 2>(1);
    const [email, setEmail] = useState("");
    const [checking, setChecking] = useState(false);
    const [emailError, setEmailError] = useState("");

    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        fullname: "",
        email: "",
        phone: "",
        position: "",
        role: "",
        interests: [] as string[],
        city: "",
        organization: "",
        subscribe_newsletter: true,
    });

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const handleEmailCheck = async (e: React.FormEvent) => {
        e.preventDefault();
        setEmailError("");
        setChecking(true);

        try {
            const response = await fetch(`/blog/${postSlug}/check-access`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-Token": csrfToken || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({ email }),
            });

            const result = await response.json();

            if (result.subscribed) {
                window.location.reload();
            } else {
                setData("email", email);
                setStep(2);
            }
        } catch {
            setEmailError(t("blog:rvClub.checkError"));
        } finally {
            setChecking(false);
        }
    };

    const handleSubscribe = (e: React.FormEvent) => {
        e.preventDefault();
        post("/newsletter/subscribe", {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => window.location.reload(),
        });
    };

    return (
        <motion.div
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
            variants={sectionVariants}
            className="max-w-2xl mx-auto text-center py-12 px-4 sm:px-6 sm:py-16 lg:py-24"
        >
            <div className="flex justify-center mb-4">
                <div className="relative">
                    <img
                        src="/assets/images/RV.png"
                        alt={t("common:company.logoAlt")}
                        className="h-10 sm:h-12 w-auto"
                        loading="lazy"
                    />
                    <div className="absolute -bottom-1 -right-1 bg-secondary text-white rounded-full p-1.5">
                        <Lock className="w-3 h-3" />
                    </div>
                </div>
            </div>

            <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-secondary bg-secondary-50 px-3 py-1 rounded-full mx-auto mb-3">
                <Lock className="w-3 h-3" />
                {t("blog:rvClub.exclusive")}
            </span>

            <h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight text-gray-900 mb-2">
                {t("common:newsletter.home.clubHeading")}
            </h2>

            <p className="text-sm sm:text-base text-gray-500 leading-relaxed mb-2">
                {t("blog:rvClub.description")}
            </p>

            <AnimatePresence mode="wait">
                {step === 1 ? (
                    <motion.form
                        key="step1"
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        transition={{ duration: 0.2 }}
                        onSubmit={handleEmailCheck}
                        className="w-full mx-auto mt-6"
                    >
                        <div className="flex flex-col gap-5 w-full">
                            <Input
                                type="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder={t("blog:rvClub.emailPlaceholder")}
                                error={emailError}
                            />
                            <Button
                                type="submit"
                                disabled={checking}
                                className="w-full"
                            >
                                <Lock className="w-4 h-4" />
                                {checking
                                    ? t("common:status.loading")
                                    : t("blog:rvClub.unlock")}
                            </Button>
                        </div>
                    </motion.form>
                ) : (
                    <motion.form
                        key="step2"
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        transition={{ duration: 0.2 }}
                        onSubmit={handleSubscribe}
                        className="w-full mx-auto mt-6"
                    >
                        <div className="flex flex-col gap-5 w-full">
                            <p className="text-sm text-gray-500 flex items-center gap-1.5 justify-center">
                                <CheckCircle2 className="w-4 h-4 text-gray-400" />
                                {t("blog:rvClub.notFound")}
                            </p>

                            <Input
                                required
                                value={data.fullname}
                                onChange={(e) =>
                                    setData("fullname", e.target.value)
                                }
                                placeholder={t(
                                    "common:newsletter.fullname.placeholder"
                                )}
                                error={errors.fullname}
                            />

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                                <Input
                                    type="email"
                                    required
                                    value={data.email}
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                    placeholder={t(
                                        "common:newsletter.email.placeholder"
                                    )}
                                    error={errors.email}
                                />
                                <Input
                                    style={{
                                        textAlign: isRtl ? "right" : "left",
                                    }}
                                    type="tel"
                                    dir="ltr"
                                    inputMode="tel"
                                    required
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData("phone", e.target.value)
                                    }
                                    placeholder={t(
                                        "common:newsletter.phone.placeholder"
                                    )}
                                    error={errors.phone}
                                />
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                                <Input
                                    value={data.position}
                                    onChange={(e) =>
                                        setData("position", e.target.value)
                                    }
                                    placeholder={t(
                                        "common:newsletter.position.placeholder"
                                    )}
                                    error={errors.position}
                                />
                                <Input
                                    value={data.city}
                                    onChange={(e) =>
                                        setData("city", e.target.value)
                                    }
                                    placeholder={t(
                                        "common:newsletter.city.placeholder"
                                    )}
                                    error={errors.city}
                                />
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                                <Select
                                    options={[
                                        {
                                            value: "public",
                                            label: t(
                                                "common:newsletter.organization.public"
                                            ),
                                        },
                                        {
                                            value: "private",
                                            label: t(
                                                "common:newsletter.organization.private"
                                            ),
                                        },
                                        {
                                            value: "nonProfit",
                                            label: t(
                                                "common:newsletter.organization.nonProfit"
                                            ),
                                        },
                                    ]}
                                    value={data.organization}
                                    onChange={(val) =>
                                        setData("organization", val)
                                    }
                                    placeholder={t(
                                        "common:newsletter.organization.placeholder"
                                    )}
                                    searchPlaceholder={t(
                                        "common:newsletter.organization.searchPlaceholder"
                                    )}
                                    noResultsText={t(
                                        "common:newsletter.organization.noResults"
                                    )}
                                    error={errors.organization}
                                />
                                <Select
                                    options={[
                                        {
                                            value: "investor",
                                            label: t(
                                                "common:newsletter.role.investor"
                                            ),
                                        },
                                        {
                                            value: "owner",
                                            label: t(
                                                "common:newsletter.role.owner"
                                            ),
                                        },
                                        {
                                            value: "ceo",
                                            label: t(
                                                "common:newsletter.role.ceo"
                                            ),
                                        },
                                        {
                                            value: "developer",
                                            label: t(
                                                "common:newsletter.role.developer"
                                            ),
                                        },
                                        {
                                            value: "consultant",
                                            label: t(
                                                "common:newsletter.role.consultant"
                                            ),
                                        },
                                        {
                                            value: "employee",
                                            label: t(
                                                "common:newsletter.role.employee"
                                            ),
                                        },
                                    ]}
                                    value={data.role}
                                    onChange={(val) => setData("role", val)}
                                    placeholder={t(
                                        "common:newsletter.role.placeholder"
                                    )}
                                    searchPlaceholder={t(
                                        "common:newsletter.role.searchPlaceholder"
                                    )}
                                    noResultsText={t(
                                        "common:newsletter.role.noResults"
                                    )}
                                    error={errors.role}
                                />
                            </div>

                            <MultiSelect
                                options={INTEREST_OPTIONS.map((opt) => ({
                                    value: opt.value,
                                    label: t(opt.labelKey),
                                }))}
                                value={data.interests}
                                onChange={(val) => setData("interests", val)}
                                placeholder={t(
                                    "common:newsletter.interests.placeholder"
                                )}
                                searchPlaceholder={t(
                                    "common:newsletter.interests.search_placeholder"
                                )}
                                noResultsText={t(
                                    "common:newsletter.interests.no_results"
                                )}
                                error={errors.interests}
                            />

                            <Checkbox
                                checked={data.subscribe_newsletter}
                                onChange={(e) =>
                                    setData(
                                        "subscribe_newsletter",
                                        e.target.checked
                                    )
                                }
                                label={t("common:newsletter.subscribe_checkbox")}
                                error={errors.subscribe_newsletter}
                                className="py-1"
                            />

                            <div className="flex flex-col sm:flex-row gap-3 w-full">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-auto md:flex-1"
                                >
                                    <Send className="w-4 h-4" />
                                    {t("navigation:footer.newsletter.subscribe")}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setStep(1)}
                                    className="sm:w-auto"
                                >
                                    <ArrowLeft className="w-4 h-4 rtl:rotate-180" />
                                    {t("blog:rvClub.backToEmail")}
                                </Button>
                            </div>
                        </div>
                    </motion.form>
                )}
            </AnimatePresence>
        </motion.div>
    );
};
