import React, { useState } from "react";
import { Head, useForm, Link } from "@inertiajs/react";

import { motion, AnimatePresence } from "framer-motion";
import {
    CheckCircle2,
    ShieldCheck,
    FileText,
    ChevronRight,
    Check,
} from "lucide-react";
import { Button } from "../../Components/ui/Button";
import { useTranslation } from "react-i18next";

interface Props {
    application: {
        uid: string;
        first_name: string;
        last_name: string;
        company_name: string;
    };
}

export default function Show({ application }: Props) {
    const { t, i18n } = useTranslation(["common", "agreement"]);
    const isArabic = i18n.language === "ar";

    const [isAgreed, setIsAgreed] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        signer_name: "",
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/agreement/${application.uid}`, {
            onSuccess: () => setIsSuccess(true),
        });
    };

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { staggerChildren: 0.1, delayChildren: 0.2 },
        },
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { type: "spring", stiffness: 100 },
        },
    };

    if (isSuccess) {
        return (
            <div
                className={`min-h-screen bg-gray-50 flex items-center justify-center p-6`}
            >
                <Head title={t("agreement:successPageTitle")} />
                <motion.div
                    initial={{ scale: 0.9, opacity: 0 }}
                    animate={{ scale: 1, opacity: 1 }}
                    className="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 text-center border border-gray-100"
                >
                    <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <Check className="w-10 h-10 text-green-600" />
                    </div>
                    <h1 className="text-3xl font-extrabold text-gray-900 mb-4">
                        {t("agreement:success.title")}
                    </h1>
                    <p className="text-gray-600 mb-8 leading-relaxed">
                        {t("agreement:success.message", {
                            name: application.first_name,
                        })}
                    </p>
                    <Link
                        href="/"
                        className="w-full h-12 bg-primary text-white hover:bg-primary-800 h-14 px-10 text-base font-bold tracking-tight rounded-md transition-all duration-300 flex items-center justify-center gap-2 active:scale-95 relative overflow-hidden"
                    >
                        {t("agreement:success.returnHome")}
                    </Link>
                </motion.div>
            </div>
        );
    }

    return (
        <div className={`min-h-screen bg-[#F8FAFC] py-12 px-6 lg:px-12`}>
            <Head title={t("agreement:pageTitle")} />

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="max-w-4xl mx-auto"
            >
                {/* Header */}
                <motion.div className="text-center mb-12">
                    <div className="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-bold uppercase tracking-wider mb-6">
                        <ShieldCheck className="w-4 h-4" />
                        {t("agreement:securePortal")}
                    </div>
                    <h1 className="text-4xl md:text-5xl font-black text-gray-900 mb-4 tracking-tight">
                        {t("agreement:investmentAgreement")}
                    </h1>
                    <p
                        className="text-gray-500 text-lg max-w-2xl mx-auto"
                        dangerouslySetInnerHTML={{
                            __html: t("agreement:reviewTermsFor", {
                                company_name: application.company_name,
                            }),
                        }}
                    />
                </motion.div>

                {/* Document Content */}
                <motion.div className="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-200 overflow-hidden mb-10">
                    <div className="p-8 md:p-12">
                        <div className="prose prose-slate max-w-none text-gray-700 leading-relaxed space-y-6">
                            <div className="flex items-center gap-3 text-gray-900 mb-8 border-b border-gray-100 pb-6">
                                <FileText className="w-8 h-8 text-primary" />
                                <div>
                                    <h2 className="text-xl font-bold m-0 leading-none">
                                        {t("agreement:partnershipAgreement")}
                                    </h2>
                                    <p className="text-sm text-gray-400 m-0 mt-1 uppercase tracking-widest font-bold">
                                        {t("agreement:documentRef", {
                                            uid: application.uid,
                                        })}
                                    </p>
                                </div>
                            </div>

                            <p
                                dangerouslySetInnerHTML={{
                                    __html: t("agreement:enteredBetween", {
                                        company_name: application.company_name,
                                        first_name: application.first_name,
                                        last_name: application.last_name,
                                    }),
                                }}
                            />

                            <h3 className="text-gray-900 font-bold text-lg">
                                {t("agreement:sections.purpose.title")}
                            </h3>
                            <p>{t("agreement:sections.purpose.content")}</p>

                            <h3 className="text-gray-900 font-bold text-lg">
                                {t("agreement:sections.confidentiality.title")}
                            </h3>
                            <p>
                                {t(
                                    "agreement:sections.confidentiality.content",
                                )}
                            </p>

                            <h3 className="text-gray-900 font-bold text-lg">
                                {t("agreement:sections.nonBinding.title")}
                            </h3>
                            <p>{t("agreement:sections.nonBinding.content")}</p>

                            <div className="bg-gray-50 rounded-2xl p-6 border border-gray-100 mt-12">
                                <p className="text-sm text-gray-500 m-0">
                                    {t("agreement:acknowledgement")}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Signing Section */}
                    <div className="bg-gray-50 border-t border-gray-200 p-8 md:p-12">
                        <form
                            onSubmit={handleSubmit}
                            className="max-w-lg mx-auto space-y-6"
                        >
                            <div className="space-y-3">
                                <label className="flex items-center gap-3 cursor-pointer group">
                                    <div
                                        className={`w-6 h-6 rounded-md border-2 flex items-center justify-center transition-all ${isAgreed ? "bg-primary border-primary" : "bg-white border-gray-300 group-hover:border-primary"}`}
                                    >
                                        <input
                                            type="checkbox"
                                            className="hidden"
                                            checked={isAgreed}
                                            onChange={() =>
                                                setIsAgreed(!isAgreed)
                                            }
                                        />
                                        {isAgreed && (
                                            <Check className="w-4 h-4 text-white" />
                                        )}
                                    </div>
                                    <span className="text-sm text-gray-600 font-medium">
                                        {t("agreement:iAgree")}
                                    </span>
                                </label>
                            </div>

                            <div className="space-y-2">
                                <label className="text-xs font-bold uppercase tracking-widest text-gray-400">
                                    {t("agreement:signatureLabel")}
                                </label>
                                <input
                                    type="text"
                                    placeholder={t(
                                        "agreement:signaturePlaceholder",
                                    )}
                                    value={data.signer_name}
                                    onChange={(e) =>
                                        setData("signer_name", e.target.value)
                                    }
                                    className={`w-full h-14 px-6 bg-white border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none transition-all rounded-xl text-lg font-['Cursive',_serif] ${isArabic ? "text-right" : "text-left"}`}
                                />
                                {errors.signer_name && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.signer_name}
                                    </p>
                                )}
                            </div>

                            <Button
                                type="submit"
                                className="w-full h-16 text-lg font-bold"
                                disabled={
                                    !isAgreed || !data.signer_name || processing
                                }
                                withArrow
                            >
                                {processing
                                    ? t("agreement:processing")
                                    : t("agreement:submitButton")}
                            </Button>
                        </form>
                    </div>
                </motion.div>
            </motion.div>
        </div>
    );
}
