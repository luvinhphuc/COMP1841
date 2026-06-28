(() => {
	const form = document.querySelector("#login-form");

	if (!form) {
		return;
	}

	const fieldNames = ["email", "password"];
	const errorSummary = form.ownerDocument.querySelector(
		"#login-error-summary",
	);
	const submitButton = form.querySelector('button[type="submit"]');
	const maxLengths = {
		email: 150,
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
					"border-[#ba1a1a]",
					"focus:border-[#ba1a1a]",
					"focus:ring-[#ba1a1a]/10",
				);
				field.classList.remove(
					"border-[#c4c7c7]",
					"focus:border-black",
					"focus:ring-black/10",
				);
			} else {
				field.classList.remove(
					"border-[#ba1a1a]",
					"focus:border-[#ba1a1a]",
					"focus:ring-[#ba1a1a]/10",
				);
				field.classList.add(
					"border-[#c4c7c7]",
					"focus:border-black",
					"focus:ring-black/10",
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
			"mb-5 rounded-lg border border-[#ba1a1a]/30 bg-[#ba1a1a]/5 p-4 text-sm leading-6 text-[#8f1111]";
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
		submitButton.textContent = submitButton.dataset.submitLabel || "Sign In";
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
		const email = form.email.value.trim();
		const password = form.password.value;
		const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		const markInvalid = (name, message) => {
			setError(name, message);
			hasError = true;
			firstInvalidField = firstInvalidField || form.elements[name];
		};

		fieldNames.forEach((name) => setError(name, ""));
		clearErrorSummary();

		if (!email) {
			markInvalid("email", "Email is required.");
		} else if (email.length > maxLengths.email) {
			markInvalid("email", "Email must be 150 characters or fewer.");
		} else if (!emailPattern.test(email)) {
			markInvalid("email", "Please enter a valid email address.");
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
