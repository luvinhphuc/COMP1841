(() => {
	const form = document.querySelector("#login-form");

	if (!form) {
		return;
	}

	const fieldNames = ["username", "password"];
	const errorSummary = form.ownerDocument.querySelector(
		"#login-error-summary",
	);
	const submitButton = form.querySelector('button[type="submit"]');
	const maxLengths = {
		username: 75,
		password: 128,
	};

	const setError = (name, message) => {
		const error = form.querySelector(`[data-error-for="${name}"]`);
		const field = form.elements[name];

		if (error) {
			const messageTarget = error.querySelector("[data-error-message]");

			if (messageTarget) {
				messageTarget.textContent = message;
			} else {
				error.textContent = message;
			}

			error.classList.toggle("hidden", !message);
			error.classList.toggle("flex", !!message);
		}

		if (field) {
			field.setAttribute("aria-invalid", message ? "true" : "false");
			if (message) {
				field.classList.add(
					"ring-[#DC2626]",
					"focus:ring-[#DC2626]/40",
				);
				field.classList.remove(
					"ring-[#E5E7EB]",
					"focus:ring-[#2563EB]/30",
				);
			} else {
				field.classList.remove(
					"ring-[#DC2626]",
					"focus:ring-[#DC2626]/40",
				);
				field.classList.add(
					"ring-[#E5E7EB]",
					"focus:ring-[#2563EB]/30",
				);
			}
		}
	};

	const showErrorSummary = (message) => {
		if (!errorSummary) {
			return;
		}

		errorSummary.textContent = message;
		errorSummary.className =
			"mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]";
		errorSummary.setAttribute("role", "alert");
		errorSummary.setAttribute("tabindex", "-1");
	};

	const clearErrorSummary = () => {
		if (!errorSummary) {
			return;
		}

		errorSummary.textContent = "";
		errorSummary.className = "sr-only";
		errorSummary.removeAttribute("role");
		errorSummary.removeAttribute("tabindex");
	};

	const resetSubmitButton = () => {
		if (!submitButton) {
			return;
		}

		form.dataset.submitting = "false";
		submitButton.disabled = false;
		submitButton.textContent = submitButton.dataset.submitLabel || "Sign in";
	};

	const setSubmitButtonLoading = () => {
		if (!submitButton) {
			return;
		}

		form.dataset.submitting = "true";
		submitButton.disabled = true;
		submitButton.textContent =
			submitButton.dataset.loadingLabel || "Signing in...";
	};

	form.addEventListener("submit", (event) => {
		if (form.dataset.submitting === "true") {
			event.preventDefault();
			return;
		}

		let hasError = false;
		let firstInvalidField = null;
		const username = form.username.value.trim();
		const password = form.password.value;

		const markInvalid = (name, message) => {
			setError(name, message);
			hasError = true;
			firstInvalidField = firstInvalidField || form.elements[name];
		};

		fieldNames.forEach((name) => setError(name, ""));
		clearErrorSummary();

		if (!username) {
			markInvalid("username", "Username is required.");
		} else if (username.length > maxLengths.username) {
			markInvalid("username", "Username must be 75 characters or fewer.");
		}

		if (!password) {
			markInvalid("password", "Password is required.");
		} else if (password.length > maxLengths.password) {
			markInvalid("password", "Password must be 128 characters or fewer.");
		}

		if (hasError) {
			event.preventDefault();
			showErrorSummary("Please fill the highlighted fields to sign in.");

			if (firstInvalidField) {
				firstInvalidField.focus();
			}

			return;
		}

		setSubmitButtonLoading();
	});

	window.addEventListener("pageshow", resetSubmitButton);
})();
