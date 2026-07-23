document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-admin-panel]');

    if (!page) {
        return;
    }

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const submitter = event.submitter && event.submitter.getAttribute('data-confirm')
                ? event.submitter
                : form;
            const message = submitter.getAttribute('data-confirm');

            if (!message) {
                return;
            }

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const analytics = document.querySelector('[data-traffic-analytics]');

    if (!analytics) {
        return;
    }

    let periods = {};

    try {
        periods = JSON.parse(analytics.getAttribute('data-traffic-analytics') || '{}');
    } catch (error) {
        return;
    }

    const numberFormat = new Intl.NumberFormat('en-US');
    const summaryTarget = analytics.querySelector('[data-analytics-summary]');
    const chartBars = analytics.querySelector('[data-analytics-chart-bars]');
    const chartLabels = analytics.querySelector('[data-analytics-chart-labels]');
    const chartLabel = analytics.querySelector('[data-analytics-chart-label]');
    const chartTotal = analytics.querySelector('[data-analytics-chart-total]');
    const chartRange = analytics.querySelector('[data-analytics-chart-range]');
    const insightTitle = analytics.querySelector('[data-analytics-insight-title]');
    const insightCopy = analytics.querySelector('[data-analytics-insight-copy]');

    const clearChildren = (element) => {
        while (element && element.firstChild) {
            element.removeChild(element.firstChild);
        }
    };

    const createElement = (tag, className, text) => {
        const element = document.createElement(tag);

        if (className) {
            element.className = className;
        }

        if (text !== undefined) {
            element.textContent = text;
        }

        return element;
    };

    const renderSummary = (periodData) => {
        clearChildren(summaryTarget);

        (periodData.summary || []).forEach((item) => {
            const card = createElement('article', `admin-analytics-kpi admin-analytics-kpi--${item.tone || 'neutral'}`);
            card.appendChild(createElement('span', '', item.label));
            card.appendChild(createElement('strong', '', numberFormat.format(item.value)));
            card.appendChild(createElement('small', '', item.meta));
            summaryTarget.appendChild(card);
        });
    };

    const renderChart = (periodData) => {
        const series = periodData.series || {};
        const labels = series.labels || [];
        const values = series.values || [];
        const max = Math.max(1, Number(series.max || 1));

        clearChildren(chartBars);
        clearChildren(chartLabels);

        labels.forEach((label, index) => {
            const value = Number(values[index] || 0);
            const bar = createElement('button', 'admin-analytics-chart__bar');
            const height = Math.max(4, Math.round((value / max) * 100));

            bar.type = 'button';
            bar.style.setProperty('--bar-height', `${height}%`);
            bar.setAttribute('aria-label', `${label}: ${numberFormat.format(value)} visits`);
            bar.title = `${label}: ${numberFormat.format(value)} visits`;

            const valueLabel = createElement('span', 'admin-analytics-chart__value', numberFormat.format(value));
            bar.appendChild(valueLabel);
            chartBars.appendChild(bar);
        });

        if (labels.length > 0) {
            chartLabels.appendChild(createElement('small', '', labels[0]));
            chartLabels.appendChild(createElement('small', '', labels[Math.floor(labels.length / 2)] || labels[0]));
            chartLabels.appendChild(createElement('small', '', labels[labels.length - 1]));
        }

        chartLabel.textContent = `${periodData.label} Traffic`;
        chartTotal.textContent = `${numberFormat.format(series.total || 0)} visits`;
        chartRange.textContent = periodData.range || '';
    };

    const renderBreakdown = (periodData) => {
        Object.entries(periodData.breakdowns || {}).forEach(([key, rows]) => {
            const container = analytics.querySelector(`[data-analytics-breakdown="${key}"]`);
            const heading = container ? container.querySelector('h3') : null;

            if (!container || !heading) {
                return;
            }

            Array.from(container.children).forEach((child) => {
                if (child !== heading) {
                    child.remove();
                }
            });

            const total = Math.max(1, Number((periodData.series || {}).total || 0));

            if (!rows || rows.length === 0) {
                container.appendChild(createElement('p', 'admin-panel-empty', 'No tracked data for this period.'));
                return;
            }

            rows.forEach((row) => {
                const count = Number(row.total || 0);
                const percent = Math.round((count / total) * 100);
                const item = createElement('div', 'admin-analytics-row');

                item.appendChild(createElement('span', '', row.label || 'Unknown'));
                item.appendChild(createElement('strong', '', numberFormat.format(count)));

                const progress = createElement('i');
                progress.style.setProperty('--progress-width', `${Math.max(2, percent)}%`);
                item.appendChild(progress);
                container.appendChild(item);
            });
        });
    };

    const renderInsight = (periodData) => {
        const summary = periodData.summary || [];
        const visits = summary[0] || { value: 0, meta: 'No traffic' };
        const average = summary[2] || { value: 0, meta: 'No average' };
        const peak = summary[3] || { value: 0, meta: '-' };

        insightTitle.textContent = `${periodData.label} visibility`;
        insightCopy.textContent = `${numberFormat.format(visits.value)} visits in this range. Peak traffic reached ${numberFormat.format(peak.value)} visits on ${peak.meta}. Average volume is ${numberFormat.format(average.value)} ${average.meta.toLowerCase()}. ${visits.meta}.`;
    };

    const setActivePeriod = (period) => {
        const periodData = periods[period] || periods.day || Object.values(periods)[0];

        if (!periodData) {
            return;
        }

        analytics.querySelectorAll('[data-analytics-period]').forEach((button) => {
            const isActive = button.getAttribute('data-analytics-period') === period;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        renderSummary(periodData);
        renderChart(periodData);
        renderBreakdown(periodData);
        renderInsight(periodData);
    };

    analytics.querySelectorAll('[data-analytics-period]').forEach((button) => {
        button.addEventListener('click', () => {
            setActivePeriod(button.getAttribute('data-analytics-period'));
        });
    });

    setActivePeriod('day');
});
