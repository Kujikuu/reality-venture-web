import React, { useState } from "react";
import { CheckCircle2, Send } from "lucide-react";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import i18n from "i18next";
import { MultiSelect } from "./ui/MultiSelect";
import { Input } from "./ui/Input";
import { Select } from "./ui/Select";
import { Checkbox } from "./ui/Checkbox";
import { Button } from "./ui/Button";
import { INTEREST_OPTIONS } from "../lib/newsletter";

interface NewsletterSubscribeProps {
    heading?: string;
    description?: string;
    badge?: string;
    backgroundImage?: string;
    className?: string;
    sectionId?: string;
}

const DEFAULT_BACKGROUND = "/assets/images/newsletter-bg.jpg";

export const NewsletterSubscribe = ({
    heading,
    description,
    badge,
    backgroundImage = DEFAULT_BACKGROUND,
    className = "",
    sectionId,
}: NewsletterSubscribeProps) => {
    const { t } = useTranslation(["navigation", "common"]);
    const isRtl = i18n.language === "ar";
    const {
        data,
        setData,
        post,
        processing,
        errors,
        recentlySuccessful,
        reset,
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

    const [step, setStep] = useState(1);

    const handleSubmitStep1 = (e: React.FormEvent) => {
        e.preventDefault();
        setStep(2);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/newsletter/subscribe", {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };
    const displayHeading = heading ?? t("common:newsletter.home.clubHeading");
    const displayDescription =
        description ?? t("common:newsletter.home.description");

    return (
        <section
            id={sectionId}
            className={`scroll-mt-24 px-4 py-12 sm:px-8 sm:py-16 lg:p-16 ${className}`}
        >
            <div className="relative overflow-hidden rounded-2xl max-w-7xl mx-auto py-16 px-6 sm:py-20 sm:px-10 lg:py-24 lg:px-16">
                <div
                    className="absolute inset-0 bg-cover bg-center"
                    style={{ backgroundImage: `url(${backgroundImage})` }}
                    aria-hidden="true"
                />

                <div className="relative flex flex-col gap-4 backdrop-blur-xs bg-white/40 border border-white/60 rounded-2xl p-6 sm:p-10 lg:p-12 text-center max-w-2xl mx-auto">
                    <div className="flex justify-center mb-4">
                        <img
                            src="/assets/images/RV.png"
                            alt={t("common:company.logoAlt")}
                            className="h-12 w-auto"
                            loading="lazy"
                        />
                    </div>
                    <h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight text-gray-900">
                        {displayHeading}
                    </h2>

                    {recentlySuccessful ? (
                        <div className="flex items-center justify-center gap-2 text-green-700 font-medium" role="status">
                            <CheckCircle2 className="w-5 h-5" aria-hidden="true" />
                            {t("navigation:footer.newsletter.success")}
                        </div>
                    ) : step === 1 ? (
                        <form
                            onSubmit={handleSubmitStep1}
                            className="max-w-2xl w-full mx-auto"
                        >
                            <div className="flex flex-col gap-4 w-full">
                                <Input
                                    required
                                    value={data.fullname}
                                    onChange={(e) =>
                                        setData("fullname", e.target.value)
                                    }
                                    placeholder={t(
                                        "common:newsletter.fullname.placeholder",
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
                                            "common:newsletter.email.placeholder",
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
                                            "common:newsletter.phone.placeholder",
                                        )}
                                        error={errors.phone}
                                    />
                                </div>

                                <Button
                                    type="submit"
                                    className="w-full"
                                >
                                    {t("navigation:footer.newsletter.getUpdates")}
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    onClick={() => setStep(2)}
                                    className="text-sm"
                                >
                                    {t("navigation:footer.newsletter.addDetails")}
                                </Button>
                            </div>
                        </form>
                    ) : (
                        <form
                            onSubmit={handleSubmit}
                            className="max-w-2xl w-full mx-auto"
                        >
                            <div className="flex flex-col gap-4 w-full">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                                    <Input
                                        value={data.position}
                                        onChange={(e) =>
                                            setData("position", e.target.value)
                                        }
                                        placeholder={t(
                                            "common:newsletter.position.placeholder",
                                        )}
                                        error={errors.position}
                                    />
                                    <Input
                                        value={data.city}
                                        onChange={(e) =>
                                            setData("city", e.target.value)
                                        }
                                        placeholder={t(
                                            "common:newsletter.city.placeholder",
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
                                                    "common:newsletter.organization.public",
                                                ),
                                            },
                                            {
                                                value: "private",
                                                label: t(
                                                    "common:newsletter.organization.private",
                                                ),
                                            },
                                            {
                                                value: "nonProfit",
                                                label: t(
                                                    "common:newsletter.organization.nonProfit",
                                                ),
                                            },
                                        ]}
                                        value={data.organization}
                                        onChange={(val) => setData("organization", val)}
                                        placeholder={t(
                                            "common:newsletter.organization.placeholder",
                                        )}
                                        searchPlaceholder={t(
                                            "common:newsletter.organization.searchPlaceholder",
                                        )}
                                        noResultsText={t(
                                            "common:newsletter.organization.noResults",
                                        )}
                                        error={errors.organization}
                                    />
                                    <Select
                                        options={[
                                            {
                                                value: "investor",
                                                label: t(
                                                    "common:newsletter.role.investor",
                                                ),
                                            },
                                            {
                                                value: "owner",
                                                label: t(
                                                    "common:newsletter.role.owner",
                                                ),
                                            },
                                            {
                                                value: "ceo",
                                                label: t(
                                                    "common:newsletter.role.ceo",
                                                ),
                                            },
                                            {
                                                value: "developer",
                                                label: t(
                                                    "common:newsletter.role.developer",
                                                ),
                                            },
                                            {
                                                value: "consultant",
                                                label: t(
                                                    "common:newsletter.role.consultant",
                                                ),
                                            },
                                            {
                                                value: "employee",
                                                label: t(
                                                    "common:newsletter.role.employee",
                                                ),
                                            },
                                        ]}
                                        value={data.role}
                                        onChange={(val) => setData("role", val)}
                                        placeholder={t(
                                            "common:newsletter.role.placeholder",
                                        )}
                                        searchPlaceholder={t(
                                            "common:newsletter.role.searchPlaceholder",
                                        )}
                                        noResultsText={t(
                                            "common:newsletter.role.noResults",
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
                                    onChange={(val) =>
                                        setData("interests", val)
                                    }
                                    placeholder={t(
                                        "common:newsletter.interests.placeholder",
                                    )}
                                    searchPlaceholder={t(
                                        "common:newsletter.interests.search_placeholder",
                                    )}
                                    noResultsText={t(
                                        "common:newsletter.interests.no_results",
                                    )}
                                    error={errors.interests}
                                />

                                <Checkbox
                                    checked={data.subscribe_newsletter}
                                    onChange={(e) =>
                                        setData(
                                            "subscribe_newsletter",
                                            e.target.checked,
                                        )
                                    }
                                    label={t(
                                        "common:newsletter.subscribe_checkbox",
                                    )}
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
                                        {t(
                                            "navigation:footer.newsletter.subscribe",
                                        )}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setStep(1)}
                                        className="sm:w-auto"
                                    >
                                        {t("navigation:footer.newsletter.back")}
                                    </Button>
                                </div>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </section>
    );
};