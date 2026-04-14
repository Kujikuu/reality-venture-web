import React, { useState } from "react";
import { CheckCircle2, Send, ChevronDown, ChevronUp } from "lucide-react";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "./ui/MultiSelect";
import { Input } from "./ui/Input";
import { Select } from "./ui/Select";
import { Checkbox } from "./ui/Checkbox";
import { Button } from "./ui/Button";

interface NewsletterSubscribeProps {
    heading?: string;
    description?: string;
    badge?: string;
    backgroundImage?: string;
    className?: string;
    sectionId?: string;
}

const INTEREST_OPTIONS = [
    {
        value: "startups",
        labelKey: "common:newsletter.interests.options.startups",
    },
    {
        value: "proptech",
        labelKey: "common:newsletter.interests.options.proptech",
    },
    {
        value: "investment",
        labelKey: "common:newsletter.interests.options.investment",
    },
    {
        value: "venture_building",
        labelKey: "common:newsletter.interests.options.ventureBuilding",
    },
    {
        value: "technology",
        labelKey: "common:newsletter.interests.options.technology",
    },
    {
        value: "real_estate",
        labelKey: "common:newsletter.interests.options.realEstate",
    },
    {
        value: "entrepreneurship",
        labelKey: "common:newsletter.interests.options.entrepreneurship",
    },
    {
        value: "innovation",
        labelKey: "common:newsletter.interests.options.innovation",
    },
];

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
        interests: [] as string[],
        city: "",
        sector: "",
        subscribe_newsletter: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/newsletter/subscribe", {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };
    const displayHeading = heading ?? t("common:newsletter.clubHeading");
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
                        />
                    </div>
                    <h2 className="text-2xl sm:text-3xl font-black text-gray-900 mb-4 sm:mb-6 tracking-tight leading-tight uppercase">
                        {displayHeading}
                    </h2>

                    {recentlySuccessful ? (
                        <div className="flex items-center justify-center gap-2 text-green-300 font-medium">
                            <CheckCircle2 className="w-5 h-5" />
                            {t("navigation:footer.newsletter.success")}
                        </div>
                    ) : (
                        <form
                            onSubmit={handleSubmit}
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

                                <div className="flex flex-col sm:flex-row gap-4 w-full">
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
                                        containerClassName="flex-1"
                                    />
                                    <Input
                                        type="tel"
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
                                        containerClassName="flex-1"
                                    />
                                </div>

                                <div className="flex flex-col sm:flex-row gap-4 w-full">
                                    <Input
                                        required
                                        value={data.position}
                                        onChange={(e) =>
                                            setData("position", e.target.value)
                                        }
                                        placeholder={t(
                                            "common:newsletter.position.placeholder",
                                        )}
                                        error={errors.position}
                                        containerClassName="flex-1"
                                    />
                                    <Input
                                        required
                                        value={data.city}
                                        onChange={(e) =>
                                            setData("city", e.target.value)
                                        }
                                        placeholder={t(
                                            "common:newsletter.city.placeholder",
                                        )}
                                        error={errors.city}
                                        containerClassName="flex-1"
                                    />
                                </div>

                                <Select
                                    options={[
                                        {
                                            value: "public",
                                            label: t(
                                                "common:newsletter.sector.public",
                                            ),
                                        },
                                        {
                                            value: "private",
                                            label: t(
                                                "common:newsletter.sector.private",
                                            ),
                                        },
                                    ]}
                                    value={data.sector}
                                    onChange={(val) => setData("sector", val)}
                                    placeholder={t(
                                        "common:newsletter.sector.placeholder",
                                    )}
                                    searchPlaceholder={t(
                                        "common:newsletter.interests.search_placeholder",
                                    )}
                                    noResultsText={t(
                                        "common:newsletter.interests.no_results",
                                    )}
                                    error={errors.sector}
                                />

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
                                    required
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

                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full h-12 disabled:opacity-50"
                                >
                                    <Send className="w-4 h-4" />
                                    {t(
                                        "navigation:footer.newsletter.subscribe",
                                    )}
                                </Button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </section>
    );
};
