import React, { useState } from "react";
import { Button } from "../Components/ui/Button";
import { Input } from "../Components/ui/Input";
import { Select } from "../Components/ui/Select";
import { Textarea } from "../Components/ui/Textarea";
import { Mail, Check, MessageCircle, CheckCircle2 } from "lucide-react";
import { motion } from "framer-motion";
import { useTranslation } from "react-i18next";
import {
    heroContainerVariants,
    heroItemVariants,
} from "../Components/animations/HeroAnimations";
import { useForm, Link, usePage } from "@inertiajs/react";
import { SEO } from "../Components/SEO";
import { SAUDI_CITIES } from "../data/saudi-cities";

export default function Apply() {
    const { t, i18n } = useTranslation(["common", "navigation", "apply"]);

    const { flash } = usePage<any>().props;
    const [isSuccess, setIsSuccess] = useState(flash?.success === 'submitted');
    const { data, setData, post, processing, errors, reset } = useForm({
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        city: "",
        social_profile: "",
        description: "",
    });

    const isArabic = i18n.language === "ar";

    const cityOptions = SAUDI_CITIES.map((c) => ({
        value: c.code,
        label: isArabic ? c.name_ar : c.name_en,
    }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/applications", {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setIsSuccess(true);
            },
        });
    };

    if (isSuccess) {
        return (
            <>
                <SEO />
                <div className="min-h-screen bg-gray-50 flex items-center justify-center p-6">
                    <motion.div
                        initial={{ scale: 0.9, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        className="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 text-center border border-gray-100"
                    >
                        <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <Check className="w-10 h-10 text-green-600" />
                        </div>
                        <h1 className="text-3xl font-extrabold text-gray-900 mb-4">
                            {t("apply:form.successTitle")}
                        </h1>
                        <p className="text-gray-600 mb-8 leading-relaxed">
                            {t("apply:form.success")}
                        </p>
                        <Link
                            href="/"
                            className="w-full h-14 bg-primary text-white hover:bg-primary-700 px-10 text-base font-bold tracking-tight rounded-xl transition-all duration-300 flex items-center justify-center gap-2 active:scale-95 shadow-lg shadow-primary/20"
                        >
                            {t("apply:form.returnHome")}
                        </Link>
                    </motion.div>
                </div>
            </>
        );
    }

    return (
        <>
            <SEO />
            <div className="flex flex-col min-h-screen bg-white">
                {/* Hero Section */}
                <section className="relative overflow-hidden pt-24 pb-16">
                    <div className="relative max-w-7xl mx-auto px-6 lg:px-6">
                        <motion.div
                            className="flex items-center"
                            variants={heroContainerVariants}
                            initial="hidden"
                            animate="visible"
                        >
                            {/* Left Content */}
                            <div>
                                {/* <motion.span
                  variants={heroItemVariants}
                  className="inline-block py-1 px-3 rounded-md bg-primary-50 text-primary text-xs font-bold tracking-wide mb-6 w-fit uppercase"
                >
                  {t('apply:hero.badge')}
                </motion.span> */}

                                <motion.h1
                                    variants={heroItemVariants}
                                    className="flex flex-wrap gap-3 text-5xl md:text-7xl font-bold tracking-tight text-gray-900 leading-[1.1] mb-6"
                                >
                                    {t("apply:hero.title")}
                                    <span className="text-primary">
                                        {t("apply:hero.titleHighlighted")}
                                    </span>
                                </motion.h1>

                                <motion.p
                                    variants={heroItemVariants}
                                    className="text-gray-500 text-lg max-w-2xl mb-8 leading-relaxed"
                                >
                                    {t("apply:hero.subtitle")}
                                </motion.p>

                                {/* <motion.div
                  variants={heroItemVariants}
                  className="flex flex-wrap gap-6 text-xs font-bold uppercase tracking-widest text-gray-400"
                >
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.rollingReview')}</span>
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.responseTime')}</span>
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.founderFirst')}</span>
                </motion.div> */}
                            </div>

                            {/* Right Content (Card) */}
                            {/* <motion.div
                variants={heroItemVariants}
                className="bg-white/80 backdrop-blur-sm border border-gray-100 shadow-xl shadow-primary-500/5 p-8 lg:p-10 rounded-3xl relative overflow-hidden"
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-full blur-3xl -z-10 opacity-50"></div>
                <h2 className="text-xl font-bold uppercase tracking-tight mb-6 text-gray-900">{t('apply:whatWeLookFor.title')}</h2>
                <ul className="space-y-4 text-sm text-gray-600">
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.innovative.description')}</li>
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.committed.description')}</li>
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.scalable.description')}</li>
                </ul>
                <div className="mt-8 pt-6 border-t border-gray-100 space-y-3 text-xs uppercase tracking-widest text-gray-400">
                  <div className="flex items-center gap-3">
                    <Mail className="w-4 h-4 text-primary" /> {t('apply:sidebar.email')}
                  </div>
                  <div className="flex items-center gap-3">
                    <MessageCircle className="w-4 h-4 text-primary" /> {t('apply:sidebar.whatsapp')}
                  </div>
                </div>
              </motion.div> */}
                        </motion.div>
                    </div>
                </section>

                {/* Form Section */}
                <section className="flex-1 py-20 px-6 lg:px-12 bg-gray-50/50">
                    <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[1.4fr_0.6fr] gap-10">
                        <div className="bg-white border border-gray-200 p-8 md:p-12 rounded-xl shadow-xs">
                            <div className="flex items-center justify-between mb-10">
                                <h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight text-gray-900">
                                    {t("apply:form.title")}
                                </h2>
                            </div>
                            <form onSubmit={handleSubmit} className="space-y-8">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <Input
                                        label={t("apply:form.firstName")}
                                        value={data.first_name}
                                        onChange={(e) =>
                                            setData(
                                                "first_name",
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            "apply:form.firstName",
                                        )}
                                        error={errors.first_name ? t("apply:" + errors.first_name, errors.first_name) : undefined}
                                    />
                                    <Input
                                        label={t("apply:form.lastName")}
                                        value={data.last_name}
                                        onChange={(e) =>
                                            setData(
                                                "last_name",
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            "apply:form.lastName",
                                        )}
                                        error={errors.last_name ? t("apply:" + errors.last_name, errors.last_name) : undefined}
                                    />
                                </div>

                                <Input
                                    type="email"
                                    label={t("apply:form.email")}
                                    value={data.email}
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                    placeholder={t(
                                        "apply:form.emailPlaceholder",
                                    )}
                                    error={errors.email ? t("apply:" + errors.email, errors.email) : undefined}
                                />

                                <Input
                                    type="tel"
                                    dir="ltr"
                                    label={t("apply:form.phone")}
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData("phone", e.target.value)
                                    }
                                    placeholder={t(
                                        "apply:form.phonePlaceholder",
                                    )}
                                    error={errors.phone ? t("apply:" + errors.phone, errors.phone) : undefined}
                                />

                                <Select
                                    label={t("apply:form.city")}
                                    value={data.city}
                                    onChange={(e) =>
                                        setData("city", e.target.value)
                                    }
                                    options={cityOptions}
                                    placeholder={t(
                                        "apply:form.cityPlaceholder",
                                    )}
                                    error={errors.city ? t("apply:" + errors.city, errors.city) : undefined}
                                />

                                <Input
                                    label={t("apply:form.linkedin")}
                                    value={data.social_profile}
                                    onChange={(e) =>
                                        setData(
                                            "social_profile",
                                            e.target.value,
                                        )
                                    }
                                    placeholder={t(
                                        "apply:form.linkedinPlaceholder",
                                    )}
                                    error={errors.social_profile ? t("apply:" + errors.social_profile, errors.social_profile) : undefined}
                                />

                                <Textarea
                                    label={t("apply:form.describe")}
                                    rows={6}
                                    value={data.description}
                                    onChange={(e) =>
                                        setData(
                                            "description",
                                            e.target.value,
                                        )
                                    }
                                    placeholder={t(
                                        "apply:form.messagePlaceholder",
                                    )}
                                    error={errors.description ? t("apply:" + errors.description, errors.description) : undefined}
                                />

                                <div className="pt-4 flex flex-col gap-4">
                                    <Button
                                        type="submit"
                                        className="w-full md:w-auto h-14 px-8"
                                        withArrow
                                        disabled={processing}
                                    >
                                        {processing
                                            ? t(
                                                  "apply:form.submitting",
                                                  "Submitting...",
                                              )
                                            : t("apply:form.submit")}
                                    </Button>
                                    {/* <p className="text-xs text-gray-400">
                                        {t("apply:form.agreement")}{" "}
                                        {t("common:footer.privacyPolicy")}{" "}
                                        {t("common:and")}{" "}
                                        {t("common:footer.termsOfService")}.
                                    </p> */}
                                </div>
                            </form>
                        </div>

                        <aside className="space-y-6">
                            <div className="border border-gray-200 bg-white p-8 rounded-xl shadow-xs">
                                <h3 className="text-lg font-bold uppercase tracking-tight mb-6 text-gray-900">
                                    {t("apply:sidebar.title")}
                                </h3>
                                <div className="space-y-5">
                                    <div className="flex items-start gap-4">
                                        <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                                        <p className="text-sm text-gray-600 leading-relaxed">
                                            {t("apply:sidebar.reviewProcess")}
                                        </p>
                                    </div>
                                    <div className="flex items-start gap-4">
                                        <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                                        <p className="text-sm text-gray-600 leading-relaxed">
                                            {t("apply:sidebar.ndaPolicy")}
                                        </p>
                                    </div>
                                    <div className="flex items-start gap-4">
                                        <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                                        <p className="text-sm text-gray-600 leading-relaxed">
                                            {t("apply:sidebar.responseTime")}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-primary text-white p-8 rounded-xl shadow-lg">
                                <h3 className="text-lg font-bold uppercase tracking-tight mb-3">
                                    {t("apply:sidebar.title")}
                                </h3>
                                <p className="text-sm text-gray-50 mb-6 leading-relaxed">
                                    {t("apply:sidebar.description")}
                                </p>
                                <div className="space-y-3">
                                    <div className="flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-white">
                                        <Mail className="w-4 h-4" />{" "}
                                        <a href="mailto:be@rv.com.sa">
                                            {t("apply:sidebar.email")}
                                        </a>
                                    </div>
                                    <div className="flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-white">
                                        <MessageCircle className="w-4 h-4" />
                                        <a
                                            href={`https://wa.me/${t("apply:sidebar.whatsappNumber").replace(/\D/g, "")}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            dir="ltr"
                                        >
                                            {t("apply:sidebar.whatsapp")}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </section>
            </div>
        </>
    );
}
