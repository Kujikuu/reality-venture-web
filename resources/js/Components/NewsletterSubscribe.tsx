import React, { useState } from "react";
import { CheckCircle2, Send, ChevronDown, ChevronUp } from "lucide-react";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";

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
    const [showClubFields, setShowClubFields] = useState(false);
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
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/newsletter/subscribe", {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const toggleInterest = (value: string) => {
        const current = data.interests;
        if (current.includes(value)) {
            setData(
                "interests",
                current.filter((i) => i !== value),
            );
        } else {
            setData("interests", [...current, value]);
        }
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
                            <div className="flex flex-col gap-3 w-full">
                                <div>
                                    <input
                                        type="text"
                                        value={data.fullname}
                                        onChange={(e) =>
                                            setData("fullname", e.target.value)
                                        }
                                        placeholder={t(
                                            "common:newsletter.fullname.placeholder",
                                        )}
                                        aria-label={t(
                                            "common:newsletter.fullname.label",
                                        )}
                                        className="w-full px-4 py-3 rounded-md bg-white border border-gray-500 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm"
                                    />
                                    {errors.fullname && (
                                        <p className="text-red-500 text-sm mt-2 text-start">
                                            {errors.fullname}
                                        </p>
                                    )}
                                </div>
                                <div className="flex flex-col sm:flex-row gap-3 w-full">
                                    <div className="flex-1">
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            placeholder={t(
                                                "common:newsletter.email.placeholder",
                                            )}
                                            aria-label={t(
                                                "common:newsletter.email.label",
                                            )}
                                            className="w-full px-4 py-3 rounded-md bg-white border border-gray-500 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm"
                                        />
                                        {errors.email && (
                                            <p className="text-red-500 text-sm mt-2 text-start">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex-1">
                                        <input
                                            type="tel"
                                            inputMode="tel"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            placeholder={t(
                                                "common:newsletter.phone.placeholder",
                                            )}
                                            aria-label={t(
                                                "common:newsletter.phone.label",
                                            )}
                                            className="w-full px-4 py-3 rounded-md bg-white border border-gray-500 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm"
                                        />
                                        {errors.phone && (
                                            <p className="text-red-500 text-sm mt-2 text-start">
                                                {errors.phone}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/* RV Club additional fields toggle */}
                                <button
                                    type="button"
                                    onClick={() =>
                                        setShowClubFields(!showClubFields)
                                    }
                                    className="flex items-center justify-center gap-1 text-sm text-gray-600 hover:text-gray-800 transition-colors"
                                >
                                    {t("common:newsletter.clubFields.toggle")}
                                    {showClubFields ? (
                                        <ChevronUp className="w-4 h-4" />
                                    ) : (
                                        <ChevronDown className="w-4 h-4" />
                                    )}
                                </button>

                                {showClubFields && (
                                    <div className="flex flex-col gap-3 w-full animate-in slide-in-from-top-2 duration-200">
                                        <div className="flex flex-col sm:flex-row gap-3 w-full">
                                            <div className="flex-1">
                                                <input
                                                    type="text"
                                                    value={data.position}
                                                    onChange={(e) =>
                                                        setData(
                                                            "position",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder={t(
                                                        "common:newsletter.position.placeholder",
                                                    )}
                                                    aria-label={t(
                                                        "common:newsletter.position.label",
                                                    )}
                                                    className="w-full px-4 py-3 rounded-md bg-white border border-gray-500 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm"
                                                />
                                                {errors.position && (
                                                    <p className="text-red-500 text-sm mt-2 text-start">
                                                        {errors.position}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="flex-1">
                                                <input
                                                    type="text"
                                                    value={data.city}
                                                    onChange={(e) =>
                                                        setData(
                                                            "city",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder={t(
                                                        "common:newsletter.city.placeholder",
                                                    )}
                                                    aria-label={t(
                                                        "common:newsletter.city.label",
                                                    )}
                                                    className="w-full px-4 py-3 rounded-md bg-white border border-gray-500 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm"
                                                />
                                                {errors.city && (
                                                    <p className="text-red-500 text-sm mt-2 text-start">
                                                        {errors.city}
                                                    </p>
                                                )}
                                            </div>
                                        </div>

                                        {/* Sector radio buttons */}
                                        <div className="text-start">
                                            <p className="text-sm font-medium text-gray-700 mb-2">
                                                {t(
                                                    "common:newsletter.sector.label",
                                                )}
                                            </p>
                                            <div className="flex gap-4">
                                                <label className="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                                                    <input
                                                        type="radio"
                                                        name="sector"
                                                        value="public"
                                                        checked={
                                                            data.sector ===
                                                            "public"
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                "sector",
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="accent-primary"
                                                    />
                                                    {t(
                                                        "common:newsletter.sector.public",
                                                    )}
                                                </label>
                                                <label className="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                                                    <input
                                                        type="radio"
                                                        name="sector"
                                                        value="private"
                                                        checked={
                                                            data.sector ===
                                                            "private"
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                "sector",
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="accent-primary"
                                                    />
                                                    {t(
                                                        "common:newsletter.sector.private",
                                                    )}
                                                </label>
                                            </div>
                                            {errors.sector && (
                                                <p className="text-red-500 text-sm mt-2">
                                                    {errors.sector}
                                                </p>
                                            )}
                                        </div>

                                        {/* Interests multi-select checkboxes */}
                                        <div className="text-start">
                                            <p className="text-sm font-medium text-gray-700 mb-2">
                                                {t(
                                                    "common:newsletter.interests.label",
                                                )}
                                            </p>
                                            <div className="grid grid-cols-2 gap-2">
                                                {INTEREST_OPTIONS.map(
                                                    (option) => (
                                                        <label
                                                            key={option.value}
                                                            className="flex items-center gap-2 cursor-pointer text-sm text-gray-700"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                checked={data.interests.includes(
                                                                    option.value,
                                                                )}
                                                                onChange={() =>
                                                                    toggleInterest(
                                                                        option.value,
                                                                    )
                                                                }
                                                                className="accent-primary rounded"
                                                            />
                                                            {t(option.labelKey)}
                                                        </label>
                                                    ),
                                                )}
                                            </div>
                                            {errors.interests && (
                                                <p className="text-red-500 text-sm mt-2">
                                                    {errors.interests}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full px-8 py-3 bg-primary hover:bg-primary-800 text-white font-bold rounded-md transition-all whitespace-nowrap inline-flex items-center justify-center gap-2 disabled:opacity-50"
                                >
                                    <Send className="w-4 h-4" />
                                    {t(
                                        "navigation:footer.newsletter.subscribe",
                                    )}
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </section>
    );
};
