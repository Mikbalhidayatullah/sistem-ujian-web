const getCsrfToken = () =>
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const refreshCsrfToken = async (url = '/csrf-token') => {
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Gagal memperbarui token keamanan.');
    }

    const result = await response.json();
    const nextToken = result.token;

    if (!nextToken) {
        throw new Error('Token keamanan baru tidak tersedia.');
    }

    const meta = document.querySelector('meta[name="csrf-token"]');

    if (meta) {
        meta.setAttribute('content', nextToken);
    }

    document.querySelectorAll('input[name="_token"]').forEach((input) => {
        input.value = nextToken;
    });

    return nextToken;
};

const sendJson = async (url, payload) => {
    let response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
        keepalive: true,
    });

    if (response.status === 419) {
        const nextToken = await refreshCsrfToken();

        response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': nextToken,
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
            keepalive: true,
        });
    }

    return response.json();
};

const initAutoRefreshCards = () => {
    document.querySelectorAll('[data-refresh-interval]').forEach((element) => {
        const interval = Number(element.dataset.refreshInterval || 0);
        const refreshKey = element.dataset.refreshKey;

        if (interval > 0) {
            window.setInterval(async () => {
                if (!refreshKey) {
                    return;
                }

                try {
                    const response = await fetch(window.location.href, {
                        method: 'GET',
                        headers: {
                            Accept: 'text/html',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const html = await response.text();
                    const parser = new DOMParser();
                    const nextDocument = parser.parseFromString(html, 'text/html');
                    const nextElement = nextDocument.querySelector(`[data-refresh-key="${refreshKey}"]`);

                    if (nextElement) {
                        element.innerHTML = nextElement.innerHTML;
                    }
                } catch (error) {
                    console.error('Gagal memperbarui panel otomatis', error);
                }
            }, interval);
        }
    });
};

const initThemeToggle = () => {
    const root = document.documentElement;
    const body = document.body;
    const toggleButtons = document.querySelectorAll('[data-theme-toggle]');

    if (!body) {
        return;
    }

    const lightIcon = `
        <svg class="theme-toggle-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3v2.2M12 18.8V21M5.64 5.64l1.56 1.56M16.8 16.8l1.56 1.56M3 12h2.2M18.8 12H21M5.64 18.36 7.2 16.8M16.8 7.2l1.56-1.56" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="1.8" />
        </svg>
    `;
    const darkIcon = `
        <svg class="theme-toggle-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20 15.2A7.8 7.8 0 0 1 8.8 4 8.6 8.6 0 1 0 20 15.2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    `;

    const readStoredTheme = () => {
        try {
            const storedTheme = window.localStorage.getItem('ui-theme');

            return ['light', 'dark'].includes(storedTheme) ? storedTheme : null;
        } catch (error) {
            return null;
        }
    };

    const writeStoredTheme = (theme) => {
        try {
            window.localStorage.setItem('ui-theme', theme);
        } catch (error) {
            return null;
        }
    };

    const defaultTheme = body.dataset.defaultTheme === 'dark' ? 'dark' : 'light';

    const applyTheme = (theme) => {
        const activeTheme = theme === 'dark' ? 'dark' : 'light';
        const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
        const label = nextTheme === 'dark' ? 'Aktifkan mode gelap' : 'Aktifkan mode terang';

        root.dataset.theme = activeTheme;
        root.style.colorScheme = activeTheme;
        writeStoredTheme(activeTheme);

        toggleButtons.forEach((button) => {
            button.innerHTML = `${nextTheme === 'dark' ? darkIcon : lightIcon}<span class="sr-only">${label}</span>`;
            button.setAttribute('aria-label', label);
            button.setAttribute('aria-pressed', String(activeTheme === 'dark'));
            button.dataset.themeState = activeTheme;
        });
    };

    applyTheme(readStoredTheme() ?? root.dataset.theme ?? defaultTheme);

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';

            applyTheme(nextTheme);
        });
    });
};

const initScrollTopButton = () => {
    const button = document.querySelector('[data-scroll-top]');

    if (!button) {
        return;
    }

    const toggleVisibility = () => {
        const shouldShow = window.scrollY > 320;

        button.classList.toggle('pointer-events-none', !shouldShow);
        button.classList.toggle('opacity-0', !shouldShow);
        button.classList.toggle('translate-y-4', !shouldShow);
        button.classList.toggle('pointer-events-auto', shouldShow);
        button.classList.toggle('opacity-100', shouldShow);
        button.classList.toggle('translate-y-0', shouldShow);
    };

    button.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    });

    window.addEventListener('scroll', toggleVisibility, { passive: true });
    toggleVisibility();
};

const initPasswordToggles = () => {
    const visibleIcon = `
        <svg class="password-toggle-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M2 12C3.8 8.5 7.4 6 12 6s8.2 2.5 10 6c-1.8 3.5-5.4 6-10 6s-8.2-2.5-10-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" />
        </svg>
    `;
    const hiddenIcon = `
        <svg class="password-toggle-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            <path d="M10.6 6.3A10.8 10.8 0 0 1 12 6c4.6 0 8.2 2.5 10 6a11.8 11.8 0 0 1-4 4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M6.7 6.8C4.7 8 3.1 9.7 2 12c1.8 3.5 5.4 6 10 6 1.4 0 2.7-.2 3.9-.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9.9 9.9A3 3 0 0 0 14.1 14.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    `;

    document.querySelectorAll('[data-password-field]').forEach((field) => {
        const input = field.querySelector('input');
        const button = field.querySelector('[data-password-toggle]');

        if (!input || !button) {
            return;
        }

        const updateState = () => {
            const isVisible = input.type === 'text';
            const label = isVisible ? 'Sembunyikan password' : 'Lihat password';

            button.innerHTML = `${isVisible ? hiddenIcon : visibleIcon}<span class="sr-only">${label}</span>`;
            button.setAttribute('aria-label', label);
            button.setAttribute('aria-pressed', String(isVisible));
        };

        button.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            updateState();
        });

        updateState();
    });
};

const initDashboardShell = () => {
    const shell = document.querySelector('[data-dashboard-shell]');

    if (!shell) {
        return;
    }

    const sidebar = shell.querySelector('[data-dashboard-sidebar]');
    const overlay = shell.querySelector('[data-dashboard-overlay]');
    const openButtons = shell.querySelectorAll('[data-dashboard-open]');
    const closeButtons = shell.querySelectorAll('[data-dashboard-close]');

    if (!sidebar || !overlay) {
        return;
    }

    const setOpen = (isOpen) => {
        shell.dataset.sidebarOpen = isOpen ? 'true' : 'false';
        sidebar.classList.toggle('translate-x-0', isOpen);
        sidebar.classList.toggle('-translate-x-full', !isOpen);
        overlay.classList.toggle('opacity-100', isOpen);
        overlay.classList.toggle('pointer-events-auto', isOpen);
        overlay.classList.toggle('opacity-0', !isOpen);
        overlay.classList.toggle('pointer-events-none', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen && window.innerWidth < 1024);
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => setOpen(true));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    setOpen(false);
};

const initExamAccessForm = () => {
    const form = document.querySelector('[data-exam-access-form]');

    if (!form) {
        return;
    }

    const tokenInput = form.querySelector('input[name="access_token"]');
    const pinInput = form.querySelector('input[name="access_pin"]');
    const submitButton = form.querySelector('[data-exam-access-submit]');
    const statusTarget = form.querySelector('[data-exam-access-status]');
    const statusUrl = form.dataset.statusUrl;
    const csrfUrl = form.dataset.csrfUrl || '/csrf-token';
    let statusPoller = null;
    let isSubmitting = false;
    let requestVersion = 0;

    const setStatus = (message, state = 'idle') => {
        if (!statusTarget) {
            return;
        }

        const stateClasses = {
            idle: 'border-white/10 bg-white/5 text-slate-300',
            invalid: 'border-amber-300/20 bg-amber-400/10 text-amber-100',
            not_found: 'border-rose-300/20 bg-rose-400/10 text-rose-100',
            empty: 'border-amber-300/20 bg-amber-400/10 text-amber-100',
            closed: 'border-rose-300/20 bg-rose-400/10 text-rose-100',
            scheduled: 'border-cyan-300/20 bg-cyan-400/10 text-cyan-100',
            open: 'border-emerald-300/20 bg-emerald-400/10 text-emerald-100',
            error: 'border-rose-300/20 bg-rose-400/10 text-rose-100',
        };

        statusTarget.className = `rounded-2xl border px-4 py-3 text-sm ${stateClasses[state] ?? stateClasses.idle}`;
        statusTarget.textContent = message;
    };

    const updateStatus = async () => {
        const accessToken = tokenInput?.value.trim() ?? '';
        const accessPin = pinInput?.value.trim() ?? '';

        if (accessToken.length < 4 || accessPin.length < 6) {
            submitButton?.removeAttribute('disabled');
            submitButton?.classList.remove('opacity-60', 'cursor-not-allowed');
            setStatus('Isi token dan PIN, lalu status ujian akan dicek otomatis.', 'idle');
            return false;
        }

        const currentRequest = ++requestVersion;

        try {
            const url = new URL(statusUrl, window.location.origin);
            url.searchParams.set('access_token', accessToken);
            url.searchParams.set('access_pin', accessPin);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            const result = await response.json();

            if (currentRequest !== requestVersion) {
                return;
            }

            const canStart = Boolean(result.can_start);

            submitButton?.toggleAttribute('disabled', !canStart);
            submitButton?.classList.toggle('opacity-60', !canStart);
            submitButton?.classList.toggle('cursor-not-allowed', !canStart);
            setStatus(result.meta ? `${result.message} ${result.meta}` : result.message, result.state);
            return canStart;
        } catch (error) {
            if (currentRequest !== requestVersion) {
                return false;
            }

            submitButton?.removeAttribute('disabled');
            submitButton?.classList.remove('opacity-60', 'cursor-not-allowed');
            setStatus('Status ujian belum bisa diperbarui. Coba lagi sebentar.', 'error');
            return true;
        }
    };

    const restartPolling = () => {
        if (statusPoller) {
            window.clearInterval(statusPoller);
        }

        void updateStatus();
        statusPoller = window.setInterval(updateStatus, 1000);
    };

    tokenInput?.addEventListener('input', restartPolling);
    pinInput?.addEventListener('input', restartPolling);
    window.addEventListener('focus', () => {
        void updateStatus();
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            void updateStatus();
        }
    });

    form.addEventListener('submit', async (event) => {
        if (isSubmitting) {
            return;
        }

        event.preventDefault();

        const canStart = await updateStatus();

        if (!canStart) {
            setStatus('Ujian belum bisa dimasuki. Tunggu sampai status berubah menjadi dibuka.', 'closed');
            return;
        }

        isSubmitting = true;
        submitButton?.setAttribute('disabled', 'disabled');
        submitButton?.classList.add('opacity-60', 'cursor-not-allowed');
        setStatus('Mempersiapkan akses ujian...', 'open');

        try {
            await refreshCsrfToken(csrfUrl);
            HTMLFormElement.prototype.submit.call(form);
        } catch (error) {
            isSubmitting = false;
            submitButton?.removeAttribute('disabled');
            submitButton?.classList.remove('opacity-60', 'cursor-not-allowed');
            setStatus('Token keamanan gagal diperbarui. Silakan coba lagi.', 'error');
        }
    });

    restartPolling();
};

const initExamSession = () => {
    const root = document.querySelector('[data-exam-session]');

    if (!root) {
        return;
    }

    const form = document.getElementById('exam-form');
    const countdownTargets = document.querySelectorAll('[data-countdown]');
    const appHeader = document.querySelector('.ui-app-header');
    const examHero = root.querySelector('[data-exam-hero]');
    const examSessionDock = root.querySelector('[data-exam-session-dock]');
    const examSessionDockStack = root.querySelector('[data-exam-session-dock-stack]');
    const questionCards = root.querySelectorAll('[data-question-card]');
    const questionNavButtons = root.querySelectorAll('[data-question-nav-button]');
    const questionNavDock = root.querySelector('[data-question-nav]');
    const questionNavTrack = root.querySelector('[data-question-nav-track]');
    const questionNavToggle = root.querySelector('[data-question-nav-toggle]');
    const questionNavToggleLabel = root.querySelector('[data-question-nav-toggle-label]');
    const previousQuestionButton = root.querySelector('[data-question-nav-prev]');
    const nextQuestionButton = root.querySelector('[data-question-nav-next]');
    const submitModal = root.querySelector('[data-submit-modal]');
    const submitModalMessage = root.querySelector('[data-submit-modal-message]');
    const submitConfirmButton = root.querySelector('[data-submit-confirm]');
    const submitCancelButton = root.querySelector('[data-submit-cancel]');
    const saveUrl = root.dataset.saveUrl;
    const violationUrl = root.dataset.violationUrl;
    const violationsEnabled = root.dataset.violationsEnabled !== '0';
    const expiresAt = root.dataset.expiresAt ? new Date(root.dataset.expiresAt) : null;
    const totalQuestions = Number(root.dataset.totalQuestions || 0);
    const questionIds = Array.from(questionCards).map((card) => card.dataset.questionId);
    let isSubmitting = false;
    let isRecordingViolation = false;
    let hasConfirmedSubmit = false;
    let activeQuestionId = questionIds[0] ?? null;
    let fullscreenRequested = false;
    let isQuestionNavExpanded = false;
    let pendingNavigationQuestionId = null;
    let pendingNavigationTimer = null;

    const collectAnswers = () => {
        const payload = {};
        const formData = new FormData(form);

        for (const [key, value] of formData.entries()) {
            const match = key.match(/^answers\[(\d+)\]$/);

            if (match) {
                payload[match[1]] = Number(value);
            }
        }

        return payload;
    };

    const syncQuestionNavState = () => {
        const answers = collectAnswers();

        questionNavButtons.forEach((button) => {
            const questionId = button.dataset.questionId;
            const isAnswered = Boolean(answers[questionId]);

            button.classList.toggle('is-answered', isAnswered);
        });
    };

    const setActiveQuestion = (questionId) => {
        activeQuestionId = String(questionId);

        questionNavButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.questionId === activeQuestionId);
        });

        const activeIndex = questionIds.indexOf(activeQuestionId);
        const isFirst = activeIndex <= 0;
        const isLast = activeIndex === -1 || activeIndex >= questionIds.length - 1;

        previousQuestionButton?.toggleAttribute('disabled', isFirst);
        previousQuestionButton?.classList.toggle('is-disabled', isFirst);
        nextQuestionButton?.toggleAttribute('disabled', isLast);
        nextQuestionButton?.classList.toggle('is-disabled', isLast);
    };

    const getDockOffset = () => {
        const headerHeight = appHeader?.getBoundingClientRect().height ?? 0;
        const dockHeight = examSessionDock?.getBoundingClientRect().height ?? 0;

        return headerHeight + dockHeight + 20;
    };

    const scrollToQuestion = (questionId) => {
        const targetCard = root.querySelector(`[data-question-card][data-question-id="${questionId}"]`);

        if (!targetCard) {
            return;
        }

        pendingNavigationQuestionId = String(questionId);
        window.clearTimeout(pendingNavigationTimer);
        setActiveQuestion(questionId);

        const targetTop = window.scrollY + targetCard.getBoundingClientRect().top - getDockOffset();

        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior: 'smooth',
        });

        pendingNavigationTimer = window.setTimeout(() => {
            setActiveQuestion(questionId);
            pendingNavigationQuestionId = null;
        }, 450);
    };

    const getCurrentQuestionId = () => {
        const dockOffset = getDockOffset();
        const visibleCards = Array.from(questionCards)
            .map((card) => ({
                id: card.dataset.questionId,
                top: card.getBoundingClientRect().top,
            }))
            .filter((card) => card.top >= dockOffset - 24);

        if (visibleCards.length > 0) {
            return visibleCards[0].id;
        }

        const nearestCard = Array.from(questionCards)
            .map((card) => ({
                id: card.dataset.questionId,
                distance: Math.abs(card.getBoundingClientRect().top - dockOffset),
            }))
            .sort((left, right) => left.distance - right.distance)[0];

        return nearestCard?.id ?? activeQuestionId;
    };

    const isMobileViewport = () => window.innerWidth < 640;

    const syncQuestionNavVisibility = () => {
        if (!questionNavDock || !questionNavToggle || !questionNavTrack) {
            return;
        }

        if (!isMobileViewport()) {
            questionNavDock.dataset.mobileCollapsed = 'false';
            questionNavToggle.setAttribute('aria-expanded', 'true');
            if (questionNavToggleLabel) {
                questionNavToggleLabel.textContent = 'Tampilkan nomor';
            }
            return;
        }

        const collapsed = !isQuestionNavExpanded;
        questionNavDock.dataset.mobileCollapsed = collapsed ? 'true' : 'false';
        questionNavToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

        if (questionNavToggleLabel) {
            questionNavToggleLabel.textContent = collapsed ? 'Tampilkan nomor' : 'Sembunyikan nomor';
        }
    };

    const syncDockState = () => {
        if (!examHero || !examSessionDock || !examSessionDockStack) {
            return;
        }

        const heroRect = examHero.getBoundingClientRect();
        const dockTop = isMobileViewport() ? 2 : 4;
        const shouldFloat = heroRect.bottom <= dockTop;

        examSessionDock.style.setProperty('--exam-session-dock-top', `${dockTop}px`);
        examSessionDock.classList.toggle('is-floating', shouldFloat);
        examSessionDock.classList.toggle('is-condensed', shouldFloat);

        if (shouldFloat) {
            const dockRect = examSessionDock.getBoundingClientRect();

            examSessionDock.style.setProperty('--exam-session-dock-left', `${dockRect.left}px`);
            examSessionDock.style.setProperty('--exam-session-dock-width', `${dockRect.width}px`);
            examSessionDock.style.setProperty('--exam-session-dock-height', `${examSessionDockStack.getBoundingClientRect().height}px`);
        } else {
            examSessionDock.style.removeProperty('--exam-session-dock-left');
            examSessionDock.style.removeProperty('--exam-session-dock-width');
            examSessionDock.style.removeProperty('--exam-session-dock-height');
        }
    };

    const playAlarm = () => {
        try {
            const context = new window.AudioContext();
            const oscillator = context.createOscillator();
            const gainNode = context.createGain();

            oscillator.type = 'square';
            oscillator.frequency.value = 900;
            gainNode.gain.value = 0.08;

            oscillator.connect(gainNode);
            gainNode.connect(context.destination);
            oscillator.start();

            window.setTimeout(() => {
                oscillator.stop();
                context.close();
            }, 700);
        } catch (error) {
            console.error('Alarm gagal diputar', error);
        }
    };

    const saveProgress = async (force = false) => {
        if (isSubmitting && !force) {
            return;
        }

        try {
            await sendJson(saveUrl, {
                answers: collectAnswers(),
            });
        } catch (error) {
            console.error('Gagal menyimpan jawaban', error);
        }
    };

    const finalizeSubmission = async () => {
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        await saveProgress(true);
        form.submit();
    };

    const submitExam = () => {
        void finalizeSubmission();
    };

    const closeSubmitModal = () => {
        submitModal?.classList.add('hidden');
        submitModal?.classList.remove('flex');
    };

    const openSubmitModal = (message) => {
        if (!submitModal || !submitModalMessage) {
            return;
        }

        submitModalMessage.textContent = message;
        submitModal.classList.remove('hidden');
        submitModal.classList.add('flex');
    };

    const recordViolation = async (violationType, detail) => {
        if (!violationsEnabled || isSubmitting || isRecordingViolation) {
            return;
        }

        isRecordingViolation = true;
        playAlarm();

        try {
            const result = await sendJson(violationUrl, {
                violation_type: violationType,
                detail,
                answers: collectAnswers(),
            });

            if (result.auto_submit) {
                submitExam();
                return;
            }
        } catch (error) {
            console.error('Gagal merekam pelanggaran', error);
        } finally {
            isRecordingViolation = false;
        }
    };

    const requestFullscreen = async () => {
        if (document.fullscreenElement || fullscreenRequested) {
            return;
        }

        fullscreenRequested = true;

        try {
            await document.documentElement.requestFullscreen();
        } catch (error) {
            fullscreenRequested = false;
            console.error('Fullscreen ditolak', error);
        }
    };

    const updateCountdown = () => {
        if (!expiresAt || countdownTargets.length === 0) {
            return;
        }

        const remaining = expiresAt.getTime() - Date.now();

        if (remaining <= 0) {
            countdownTargets.forEach((target) => {
                target.textContent = 'Waktu habis, ujian akan dikumpulkan.';
            });
            submitExam();
            return;
        }

        const totalSeconds = Math.floor(remaining / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        const countdownState = totalSeconds <= 60
            ? 'critical'
            : (totalSeconds <= 300
                ? 'danger'
                : (totalSeconds <= 900 ? 'warning' : 'normal'));

        const timeText = `Sisa waktu ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        countdownTargets.forEach((target) => {
            target.textContent = timeText;
            target.dataset.countdownState = countdownState;
            target.closest('.exam-chip')?.setAttribute('data-countdown-state', countdownState);
            target.closest('.exam-timer-dock')?.setAttribute('data-countdown-state', countdownState);
        });
    };

    questionNavButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            scrollToQuestion(button.dataset.questionId);

            if (isMobileViewport()) {
                isQuestionNavExpanded = false;
                syncQuestionNavVisibility();
            }
        });
    });

    previousQuestionButton?.addEventListener('click', () => {
        const currentQuestionId = getCurrentQuestionId();
        const activeIndex = questionIds.indexOf(String(currentQuestionId));

        if (activeIndex > 0) {
            scrollToQuestion(questionIds[activeIndex - 1]);
        }
    });

    nextQuestionButton?.addEventListener('click', () => {
        const currentQuestionId = getCurrentQuestionId();
        const activeIndex = questionIds.indexOf(String(currentQuestionId));

        if (activeIndex !== -1 && activeIndex < questionIds.length - 1) {
            scrollToQuestion(questionIds[activeIndex + 1]);
        }
    });

    questionNavToggle?.addEventListener('click', () => {
        if (!isMobileViewport()) {
            return;
        }

        isQuestionNavExpanded = !isQuestionNavExpanded;
        syncQuestionNavVisibility();
    });

    form.querySelectorAll('input[type="radio"][name^="answers["]').forEach((input) => {
        input.addEventListener('change', syncQuestionNavState);
    });

    if (questionCards.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            const visibleEntry = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

            if (visibleEntry) {
                const visibleQuestionId = String(visibleEntry.target.dataset.questionId);

                if (pendingNavigationQuestionId && visibleQuestionId !== pendingNavigationQuestionId) {
                    return;
                }

                setActiveQuestion(visibleEntry.target.dataset.questionId);
            }
        }, {
            root: null,
            threshold: [0.2, 0.45, 0.7],
            rootMargin: '-20% 0px -55% 0px',
        });

        questionCards.forEach((card) => observer.observe(card));
        setActiveQuestion(questionCards[0].dataset.questionId);
    }

    if (examHero && examSessionDock) {
        syncDockState();
        window.addEventListener('scroll', syncDockState, { passive: true });
        window.addEventListener('resize', syncDockState);
    }

    window.addEventListener('resize', syncQuestionNavVisibility);

    form.addEventListener('submit', async (event) => {
        if (hasConfirmedSubmit || isSubmitting) {
            return;
        }

        event.preventDefault();

        const answeredCount = Object.keys(collectAnswers()).length;
        const unansweredCount = Math.max(totalQuestions - answeredCount, 0);
        const confirmationMessage = unansweredCount > 0
            ? `Anda sudah menjawab ${answeredCount} dari ${totalQuestions} soal. Masih ada ${unansweredCount} soal kosong. Yakin ingin mengumpulkan ujian sekarang?`
            : `Semua ${totalQuestions} soal sudah dijawab. Yakin ingin mengumpulkan ujian sekarang?`;

        openSubmitModal(confirmationMessage);
    });

    submitCancelButton?.addEventListener('click', () => {
        closeSubmitModal();
    });

    submitConfirmButton?.addEventListener('click', async () => {
        if (isSubmitting) {
            return;
        }

        hasConfirmedSubmit = true;
        closeSubmitModal();
        await finalizeSubmission();
    });

    submitModal?.addEventListener('click', (event) => {
        if (event.target === submitModal) {
            closeSubmitModal();
        }
    });

    form.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            saveProgress();
        });
    });

    const requestFullscreenOnFirstInteraction = () => {
        if (document.fullscreenElement) {
            return;
        }

        void requestFullscreen();
    };

    ['pointerdown', 'keydown', 'touchstart'].forEach((eventName) => {
        document.addEventListener(eventName, requestFullscreenOnFirstInteraction, { once: true, passive: true });
    });

    if (violationsEnabled) {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                recordViolation('tab_hidden', 'Siswa berpindah tab atau aplikasi.');
            }
        });

        window.addEventListener('blur', () => {
            recordViolation('window_blur', 'Jendela ujian kehilangan fokus.');
        });

        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement && !isSubmitting) {
                recordViolation('fullscreen_exit', 'Siswa keluar dari mode full-screen.');
            }
        });

        document.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            recordViolation('context_menu', 'Percobaan membuka context menu.');
        });

        document.addEventListener('copy', (event) => {
            event.preventDefault();
            recordViolation('copy_attempt', 'Percobaan menyalin isi ujian.');
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && submitModal && !submitModal.classList.contains('hidden')) {
            closeSubmitModal();
            return;
        }

        const blocked =
            event.key === 'F12' ||
            (event.ctrlKey && event.shiftKey && ['I', 'J', 'C'].includes(event.key.toUpperCase())) ||
            (event.ctrlKey && ['U', 'S', 'P'].includes(event.key.toUpperCase()));

        if (blocked) {
            event.preventDefault();
            if (violationsEnabled) {
                recordViolation('blocked_shortcut', `Shortcut terlarang: ${event.key}`);
            }
        }
    });

    window.addEventListener('beforeunload', () => {
        if (isSubmitting || !violationsEnabled) {
            return;
        }

        fetch(violationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            keepalive: true,
            body: JSON.stringify({
                violation_type: 'leave_page',
                detail: 'Siswa mencoba meninggalkan halaman ujian.',
                answers: collectAnswers(),
            }),
        }).catch(() => null);
    });

    requestFullscreen();
    updateCountdown();
    syncQuestionNavState();
    syncDockState();
    syncQuestionNavVisibility();
    window.setInterval(updateCountdown, 1000);
    window.setInterval(saveProgress, 15000);
};

const initInsightResponseFilters = () => {
    document.querySelectorAll('[data-insight-response-filter]').forEach((select) => {
        const container = select.closest('.overflow-hidden');

        if (!container) {
            return;
        }

        const rows = Array.from(container.querySelectorAll('[data-insight-response-row]'));
        const emptyState = container.querySelector('[data-insight-filter-empty]');

        const updateRows = () => {
            const filter = select.value || 'all';
            let visibleCount = 0;

            rows.forEach((row) => {
                const matches = filter === 'all' || row.dataset.responseStatus === filter;

                row.classList.toggle('hidden', !matches);

                if (matches) {
                    visibleCount += 1;
                }
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0);
            }
        };

        select.addEventListener('change', updateRows);
        updateRows();
    });
};

const initQuestionBuilderTabs = () => {
    document.querySelectorAll('[data-question-builder]').forEach((builder) => {
        const buttons = Array.from(builder.querySelectorAll('[data-question-builder-tab]'));
        const panels = Array.from(builder.querySelectorAll('[data-question-builder-panel]'));

        if (buttons.length === 0 || panels.length === 0) {
            return;
        }

        const setActiveTab = (tab) => {
            const activeTab = tab === 'quick' ? 'quick' : 'manual';

            buttons.forEach((button) => {
                const isActive = button.dataset.questionBuilderTab === activeTab;

                button.classList.toggle('dashboard-button-primary', isActive);
                button.classList.toggle('dashboard-button-soft', !isActive);
                button.setAttribute('aria-pressed', String(isActive));
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.questionBuilderPanel !== activeTab);
            });
        };

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                setActiveTab(button.dataset.questionBuilderTab);
            });
        });

        setActiveTab(builder.dataset.defaultTab || 'manual');
    });
};

const initInsightSortSelect = () => {
    document.querySelectorAll('[data-insight-sort-select]').forEach((select) => {
        const list = document.querySelector('[data-insight-list]');

        if (!list) {
            return;
        }

        const getMetric = (item, key) => Number(item.dataset[key] || 0);
        const compareByPosition = (a, b) => getMetric(a, 'position') - getMetric(b, 'position');

        const applySort = () => {
            const sort = select.value || 'default';
            const items = Array.from(list.querySelectorAll('[data-insight-item]'));

            const sorted = items.sort((a, b) => {
                if (sort === 'hardest') {
                    return (
                        getMetric(b, 'wrongPercentage') - getMetric(a, 'wrongPercentage') ||
                        getMetric(b, 'unansweredPercentage') - getMetric(a, 'unansweredPercentage') ||
                        compareByPosition(a, b)
                    );
                }

                if (sort === 'easiest') {
                    return (
                        getMetric(b, 'correctPercentage') - getMetric(a, 'correctPercentage') ||
                        getMetric(a, 'wrongPercentage') - getMetric(b, 'wrongPercentage') ||
                        compareByPosition(a, b)
                    );
                }

                if (sort === 'most_wrong') {
                    return (
                        getMetric(b, 'wrongCount') - getMetric(a, 'wrongCount') ||
                        getMetric(b, 'wrongPercentage') - getMetric(a, 'wrongPercentage') ||
                        compareByPosition(a, b)
                    );
                }

                if (sort === 'most_correct') {
                    return (
                        getMetric(b, 'correctCount') - getMetric(a, 'correctCount') ||
                        getMetric(b, 'correctPercentage') - getMetric(a, 'correctPercentage') ||
                        compareByPosition(a, b)
                    );
                }

                return compareByPosition(a, b);
            });

            items.forEach((item) => {
                item.classList.add('is-sorting');
            });

            window.requestAnimationFrame(() => {
                sorted.forEach((item) => {
                    list.appendChild(item);
                });

                window.requestAnimationFrame(() => {
                    sorted.forEach((item) => {
                        item.classList.remove('is-sorting');
                    });
                });
            });
        };

        select.addEventListener('change', applySort);
        applySort();
    });
};

const initQuestionEditors = () => {
    document.querySelectorAll('[data-question-editor-panel]').forEach((panel) => {
        const card = panel.closest('.dashboard-muted-card');

        if (!card) {
            return;
        }

        const toggleButton = card.querySelector('[data-question-editor-toggle]');
        const closeButton = card.querySelector('[data-question-editor-close]');
        if (!toggleButton) {
            return;
        }

        const setOpen = (shouldOpen) => {
            panel.classList.toggle('is-open', shouldOpen);
            panel.dataset.open = shouldOpen ? 'true' : 'false';
            toggleButton.setAttribute('aria-expanded', String(shouldOpen));
            toggleButton.textContent = shouldOpen ? 'Tutup editor' : 'Edit soal';
            toggleButton.classList.toggle('dashboard-button-return', shouldOpen);
            toggleButton.classList.toggle('dashboard-button-soft', !shouldOpen);

            if (shouldOpen) {
                panel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                });
            }
        };

        const closeOtherEditors = () => {
            document.querySelectorAll('[data-question-editor-panel]').forEach((otherPanel) => {
                if (otherPanel === panel) {
                    return;
                }

                const otherCard = otherPanel.closest('.dashboard-muted-card');
                const otherToggle = otherCard?.querySelector('[data-question-editor-toggle]');

                otherPanel.classList.remove('is-open');
                otherPanel.dataset.open = 'false';

                if (otherToggle) {
                    otherToggle.setAttribute('aria-expanded', 'false');
                    otherToggle.textContent = 'Edit soal';
                }
            });
        };

        toggleButton.addEventListener('click', () => {
            const shouldOpen = panel.dataset.open !== 'true';

            if (shouldOpen) {
                closeOtherEditors();
            }

            setOpen(shouldOpen);
        });

        closeButton?.addEventListener('click', () => {
            setOpen(false);
        });
        setOpen(panel.dataset.open === 'true');
    });
};

const initQuestionOptionSelection = () => {
    document.querySelectorAll('form').forEach((form) => {
        const optionEditors = Array.from(form.querySelectorAll('[data-question-option-editor]'));
        const optionRadios = Array.from(form.querySelectorAll('[data-question-option-radio]'));

        if (optionEditors.length === 0 || optionRadios.length === 0) {
            return;
        }

        const syncSelectedOption = () => {
            optionEditors.forEach((optionEditor, index) => {
                const radio = optionRadios[index];
                optionEditor.classList.toggle('is-selected', Boolean(radio?.checked));
            });
        };

        optionRadios.forEach((radio) => {
            radio.addEventListener('change', syncSelectedOption);
        });

        syncSelectedOption();
    });
};

const initPrintSettingsPreview = () => {
    document.querySelectorAll('[data-print-settings-preview]').forEach((form) => {
        const schoolNameInput = form.querySelector('[data-print-preview-school-name]');
        const schoolDepartmentInput = form.querySelector('[data-print-preview-school-department]');
        const schoolAddressInput = form.querySelector('[data-print-preview-school-address]');
        const logoInput = form.querySelector('[data-print-preview-logo-input]');
        const removeLogoInput = form.querySelector('[data-print-preview-remove-logo]');
        const schoolNameTarget = document.querySelector('[data-print-preview-school-name-target]');
        const schoolDepartmentTarget = document.querySelector('[data-print-preview-school-department-target]');
        const schoolAddressTarget = document.querySelector('[data-print-preview-school-address-target]');
        const logoImages = Array.from(document.querySelectorAll('[data-print-preview-logo-image]'));
        const logoPlaceholders = Array.from(document.querySelectorAll('[data-print-preview-logo-placeholder]'));

        if (
            !schoolNameTarget ||
            !schoolDepartmentTarget ||
            !schoolAddressTarget ||
            logoImages.length === 0 ||
            logoPlaceholders.length === 0
        ) {
            return;
        }

        const originalLogoSrc = logoImages[0].dataset.originalSrc || '';

        const syncText = () => {
            schoolNameTarget.textContent = schoolNameInput?.value.trim() || 'Nama sekolah';
            schoolDepartmentTarget.textContent = `Jurusan : ${schoolDepartmentInput?.value.trim() || 'Jurusan'}`;
            schoolAddressTarget.textContent = schoolAddressInput?.value.trim() || 'Alamat sekolah';
        };

        const showLogoState = (src) => {
            const hasLogo = Boolean(src);

            logoImages.forEach((logoImage) => {
                if (hasLogo) {
                    logoImage.src = src;
                }

                logoImage.classList.toggle('hidden', !hasLogo);
            });

            logoPlaceholders.forEach((logoPlaceholder) => {
                logoPlaceholder.classList.toggle('hidden', hasLogo);
            });
        };

        const syncLogo = () => {
            const shouldRemoveLogo = Boolean(removeLogoInput?.checked);
            const selectedFile = logoInput?.files?.[0];

            if (selectedFile) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    showLogoState(String(event.target?.result || ''));
                };
                reader.readAsDataURL(selectedFile);
                return;
            }

            if (shouldRemoveLogo) {
                showLogoState('');
                return;
            }

            showLogoState(originalLogoSrc);
        };

        schoolNameInput?.addEventListener('input', syncText);
        schoolDepartmentInput?.addEventListener('input', syncText);
        schoolAddressInput?.addEventListener('input', syncText);
        logoInput?.addEventListener('change', syncLogo);
        removeLogoInput?.addEventListener('change', syncLogo);

        syncText();
        syncLogo();
    });
};

initThemeToggle();
initAutoRefreshCards();
initScrollTopButton();
initPasswordToggles();
initDashboardShell();
initExamAccessForm();
initExamSession();
initInsightResponseFilters();
initQuestionBuilderTabs();
initInsightSortSelect();
initQuestionEditors();
initQuestionOptionSelection();
initPrintSettingsPreview();
