(() => {
	const form = document.querySelector("#register-form");

	if (!form) {
		return;
	}

	const fieldNames = [
		"first_name",
		"last_name",
		"username",
		"email",
		"password",
		"confirm_password",
	];
	const errorSummary = form.ownerDocument.querySelector(
		"#register-error-summary",
	);
	const submitButton = form.querySelector('button[type="submit"]');
	const maxLengths = {
		first_name: 35,
		last_name: 35,
		username: 75,
		email: 150,
		password: 128,
		confirm_password: 128,
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
		submitButton.textContent =
			submitButton.dataset.submitLabel || "Create account";
	};

	const setSubmitButtonLoading = () => {
		if (!submitButton) {
			return;
		}

		form.dataset.submitting = "true";
		submitButton.disabled = true;
		submitButton.textContent =
			submitButton.dataset.loadingLabel || "Creating account...";
	};

	form.addEventListener("submit", (event) => {
		if (form.dataset.submitting === "true") {
			event.preventDefault();
			return;
		}

		let hasError = false;
		let firstInvalidField = null;
		const firstName = form.first_name.value.trim();
		const lastName = form.last_name.value.trim();
		const username = form.username.value.trim();
		const email = form.email.value.trim();
		const password = form.password.value;
		const confirmPassword = form.confirm_password.value;
		const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		const markInvalid = (name, message) => {
			setError(name, message);
			hasError = true;
			firstInvalidField = firstInvalidField || form.elements[name];
		};

		fieldNames.forEach((name) => setError(name, ""));
		clearErrorSummary();

		if (!firstName) {
			markInvalid("first_name", "First name is required.");
		} else if (firstName.length > maxLengths.first_name) {
			markInvalid("first_name", "First name must be 35 characters or fewer.");
		}

		if (!lastName) {
			markInvalid("last_name", "Last name is required.");
		} else if (lastName.length > maxLengths.last_name) {
			markInvalid("last_name", "Last name must be 35 characters or fewer.");
		}

		if (!username) {
			markInvalid("username", "Username is required.");
		} else if (username.length > maxLengths.username) {
			markInvalid("username", "Username must be 75 characters or fewer.");
		}

		if (!email) {
			markInvalid("email", "Email is required.");
		} else if (email.length > maxLengths.email) {
			markInvalid("email", "Email must be 150 characters or fewer.");
		} else if (!emailPattern.test(email)) {
			markInvalid("email", "Please enter a valid email address.");
		}

		if (!password) {
			markInvalid("password", "Password is required.");
		} else if (password.length < 8) {
			markInvalid("password", "Password must be at least 8 characters.");
		} else if (password.length > maxLengths.password) {
			markInvalid("password", "Password must be 128 characters or fewer.");
		}

		if (!confirmPassword) {
			markInvalid("confirm_password", "Please confirm your password.");
		} else if (confirmPassword.length > maxLengths.confirm_password) {
			markInvalid(
				"confirm_password",
				"Confirm password must be 128 characters or fewer.",
			);
		} else if (password !== confirmPassword) {
			markInvalid("confirm_password", "Passwords do not match.");
		}

		if (hasError) {
			event.preventDefault();
			showErrorSummary(
				"Please fill the highlighted fields to create your account.",
			);

			if (firstInvalidField) {
				firstInvalidField.focus();
			}

			return;
		}

		setSubmitButtonLoading();
	});

	window.addEventListener("pageshow", resetSubmitButton);
})();
