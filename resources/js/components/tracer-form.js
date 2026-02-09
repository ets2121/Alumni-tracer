export default (config) => ({
    answers: {},
    logicMap: config.logicMap || {},

    init() {
        // Optional: Predefine answers with defaults if needed
    },

    isVisible(questionId) {
        if (this.logicMap[questionId]) {
            const rule = this.logicMap[questionId];
            // Check if triggers match
            // Support multiple triggers? For now code assumes single trigger per question
            const triggerVal = this.answers[rule.trigger_id];

            // Check for array (radio/checkbox values)
            if (Array.isArray(triggerVal)) {
                return triggerVal.includes(rule.value);
            }
            return triggerVal === rule.value;
        }
        return true;
    }
});
